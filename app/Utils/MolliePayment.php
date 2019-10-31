<?php

namespace App\Utils;

use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Contract;
use App\Models\Customer;
use Mollie\Laravel\Facades\Mollie;

class MolliePayment
{
    public $price;
    public $tenant;
    public $contract;
    public $invoice;
    public $type;
    public $webhook;

    public function __construct(Tenant $tenant, Contract $contract, Invoice $invoice, $webhook = false)
    {
        $this->tenant = $tenant;
        $this->contract = $contract;
        $this->invoice = $invoice;
        $this->price = $this->calculatePrice();
        $this->type = $this->firstOrRecurring();
        $this->webhook = $this->createWebhook($webhook);
    }

    private function createWebhook($webhook)
    {
        return (!$webhook) ? config('app.mollie_webhook') : $webhook;
    }

    private function firstOrRecurring()
    {
        return ($this->contract->tenant->mollie_id && $this->contract->tenant->mandate_id) ? 'recurring' : 'first';
    }

    private function calculatePrice()
    {
        // calculate value
        $price = $this->contract->units->sum('pivot.price');
        if ($this->contract->tenant->kvk)
            $price = $price * 1.21; // add VAT/ BTW at 21% for companies
        return number_format($price, 2);
    }

    private function createMandate($mollieCustomer)
    {
        // create a mandate  
        $mandate = $mollieCustomer->createMandate([
            "method" => \Mollie\Api\Types\MandateMethod::DIRECTDEBIT,
            "consumerName" => $this->tenant->name,
            "consumerAccount" => str_replace(' ', '', $this->tenant->iban), // make sure there are no spaces in the iban
            "signatureDate" => \Carbon\Carbon::now()->format('Y-m-d'),
            "mandateReference" => "OPSLAGMAG-" . $this->tenant->id,
        ]);
        // save it to the tenant. The next time recurring will be used. 
        $this->tenant->update(['mandate_id' => $mandate->id]);
    }

    public function payment()
    {
        if ($this->type === 'first') {
            // create a mollie customer. 
            $mollieCustomer = Mollie::api()->customers()->create([
                "name"  => $this->tenant->name,
                "email"  => $this->tenant->email,
            ]);

            $this->tenant->update(['mollie_id' => $mollieCustomer->id]);

            // there is no mandate yet, lets create one
            $this->createMandate($mollieCustomer);
        }

        // It's either just created or already existing. In either case we need to get them again. Mollie api doesnt return customer on creating
        $mollieCustomer = Mollie::api()->customers()->get($this->tenant->mollie_id);

        // if its a recurring payment, there must a valid mandate in mollie.
        if ($this->type === 'recurring') {
            $mandates = Mollie::api()->mandates()->listFor($mollieCustomer);
            if ($mandates->count === 0) {
                activity('mollie')->log('Geen geldige mandaat gevonden in Mollie voor ' . $this->tenant->name . ', nieuwe aanmaken...');
                $this->createMandate($mollieCustomer);
            }
        }

        // create the mollie payment, if sequenceType is first; this also generates a mandate for the customer (but only on checkout urls).
        try {
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

            // TODO: save the mollie mandate to our db
        } catch (\Exception $e) {
            activity('mollie')->log('something went wrong with the payment: ' . $e);
        }
        activity('mollie')->log('Mollie betaling gemaakt met type ' . $this->type . ' voor ' . $this->tenant->name);

        // save the payment to our database
        $payment = Payment::create([
            'customer_id' => Customer::current()->id,
            'contract_id' => $this->contract->id,
            'tenant_id' => $this->tenant->id,
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
