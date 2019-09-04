<?php

namespace App\Utils;

use App\Models\Payment;
use App\Models\Contract;
use App\Models\Customer;
use Mollie\Laravel\Facades\Mollie;

class MolliePayment
{
    public $price;
    public $customer;
    public $contract;
    public $type;

    public function __construct(Customer $customer, Contract $contract, $type)
    {
        $this->customer = $customer;
        $this->contract = $contract;
        $this->price = $this->calculatePrice();
        $this->type = $type;
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

            $this->customer->update(['mollie_id' => $mollieCustomer->id]);
        }

        // if its a recurring payment, there must a valid mandate in mollie.
        if ($this->type === 'recurring') {
            if(!$this->customer->mollie_id) // there is no mollie_id so we cant check for mandates and should abort
                return;
            $mollieCustomer = Mollie::api()->customers()->get($this->customer->mollie_id);
            $mandates = Mollie::api()->mandates()->listFor($mollieCustomer);
            if (!$mandates) {
                activity()->log('Geen geldige mandaat gevonden in Mollie voor ' . $this->customer->name);
                return;
            }
        }

        // create the mollie payment, if sequenceType is first; this also generates a mandate for the customer.
        $payment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => 'EUR',
                'value' => (string) $this->price, // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            'customerId'    => $mollieCustomer->id, // the mollie customer is either created or requested via the api
            'sequenceType' => $this->type, //'first' or 'recurring',
            'description' => 'Verhuur van opslagruimte',
            'webhookUrl' => config('app.mollie_webhook'),
            'redirectUrl' => config('app.booking_complete_url'),
        ]);
        activity()->log('Mollie betaling gemaakt met type ' . $this->type . ' voor ' . $this->customer->name);

        // save the payment to our database
        Payment::create([
            'contract_id' => $this->contract->id,
            'customer_id' => $this->customer->id,
            'payment_id' => $payment->id,
        ]);

        // returns the mollie payment object, including a checkout url
        return $payment;
    }
}
