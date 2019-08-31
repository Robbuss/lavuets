<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Payment;
use App\Models\Contract;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Mail\BookingComplete;
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $free = Unit::has('contracts')->get();
        $free = Unit::doesntHave('contracts')->get();
        $grouped = $free->mapToGroups(function ($item, $key) {
            return [$item['size'] => $item];
        });
        return ['units' => $grouped, 'count' => $free->count()];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // create a customer
        $customer = Customer::create($request->customer);

        // create a contract that is inactive till after payment is received (activated in the webhook)
        $contract = Contract::create(array_merge($request->contract, [
            'customer_id' => $customer->id,
            'active' => false,
        ]));
        $contract->units()->sync($this->getSyncArray($request->units));

        // calculate value
        // add description
        // fix webhook
        $payment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => 'EUR',
                'value' => '10.00', // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            'description' => 'My first API payment',
            'webhookUrl' => config('app.mollie_webhook'),
            'redirectUrl' => config('app.booking_complete_url'),
        ]);

        // save the payment to the database
        Payment::create([
            'contract_id' => $contract->id,
            'customer_id' => $customer->id,
            'payment_id' => $payment->id,
        ]);
        
        $payment = Mollie::api()->payments()->get($payment->id);

        // redirect customer to Mollie checkout page
        return ['success' => true, 'payment_url' => $payment->getCheckoutUrl()];
    }

    public function getSyncArray($priceArray = [])
    {
        $contractUnitPrice = [];
        foreach ($priceArray as $pu) {
            $contractUnitPrice[$pu['id']] = ['price' => $pu['price']];
        };
        return $contractUnitPrice;
    }

    public function preparePayment()
    {
//
    }
}
