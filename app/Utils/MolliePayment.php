<?php

namespace App\Utils;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Contract;
use App\Models\Customer;
use Mollie\Laravel\Facades\Mollie;

class MolliePayment
{
    public $price;
    public $customer;
    public $contract;
    public $invoice;
    public $type;
    public $webhook;

    public function __construct(Customer $customer, Contract $contract, Invoice $invoice, $type, $webhook = false)
    {
        $this->customer = $customer;
        $this->contract = $contract;
        $this->invoice = $invoice;
        $this->price = $this->calculatePrice();
        $this->type = $type;

        if(!$webhook){
            $this->webhook = config('app.mollie_webhook');
        }else{
            $this->webhook = $webhook;
        }
    }

    private function calculatePrice()
    {
        // calculate value
        $price = $this->contract->units->sum('pivot.price');
        if ($this->contract->customer->kvk)
            $price = $price * 1.21; // add VAT/ BTW at 21% for companies
        return number_format($price, 2);
    }

    public function payment()
    {
        if ($this->type === 'first') {
            // create a mollie customer. 
            $mollieCustomer = Mollie::api()->customers()->create([
                "name"  => $this->customer->name,
                "email"  => $this->customer->email,
            ]);

            // i believe we need to get the mollie model again, for security's sake (something in their api docs about this)
            $mollieCustomer = Mollie::api()->customers()->get($mollieCustomer->id);

            // create a mandate  
            $mandate = $mollieCustomer->createMandate([
                "method" => \Mollie\Api\Types\MandateMethod::DIRECTDEBIT,
                "consumerName" => $this->customer->name,
                "consumerAccount" => str_replace(' ', '', $this->customer->iban), // make sure there are no spaces in the iban
                "signatureDate" => \Carbon\Carbon::now()->format('Y-m-d'),
                "mandateReference" => "OPSLAGMAG-" . $this->customer->id,
            ]);
            // save it to the customer. The next time recurring will be used. 
            $this->customer->update(['mollie_id' => $mollieCustomer->id, 'mandate_id' => $mandate->id]);
        }

        // if its a recurring payment, there must a valid mandate in mollie.
        if ($this->type === 'recurring') {
            if (!$this->customer->mollie_id) // there is no mollie_id so we cant check for mandates and should abort
                return;

            $mollieCustomer = Mollie::api()->customers()->get($this->customer->mollie_id);
            $mandates = Mollie::api()->mandates()->listFor($mollieCustomer);
            if (!$mandates) {
                activity('mollie')->log('Geen geldige mandaat gevonden in Mollie voor ' . $this->customer->name);
                return;
            }
        }

        // create the mollie payment, if sequenceType is first; this also generates a mandate for the customer.
        $molliePayment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => 'EUR',
                'value' => (string) $this->price, // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            'customerId'    => $mollieCustomer->id, // the mollie customer is either created or requested via the api
            'sequenceType' => $this->type, //'first' or 'recurring',
            'description' => 'Verhuur van opslagruimte',
            'webhookUrl' => $this->webhook,
            'redirectUrl' => config('app.booking_complete_url'),
        ]);
        activity('mollie')->log('Mollie betaling gemaakt met type ' . $this->type . ' voor ' . $this->customer->name);

        // save the payment to our database
        $payment = Payment::create([
            'contract_id' => $this->contract->id,
            'customer_id' => $this->customer->id,
            'invoice_id' => $this->invoice->id,
            'payment_id' => $molliePayment->id,
            'status' => 'pending',
            'amount' => (string) $this->price
        ]);

        // returns the mollie payment object, including a checkout url
        return [
            'molliepayment' => $molliePayment,
            'payment' => $payment
        ];
    }
}
