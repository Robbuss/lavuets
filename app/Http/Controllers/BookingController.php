<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Contract;
use App\Models\Customer;
use App\Utils\MolliePayment;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $free = Unit::doesntHave('contracts')->where('size', '>', 0)->where('active', 1)->get();
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
            'auto_invoice' => true,
        ]));
        $contract->units()->sync($this->getSyncArray($request->units));

        // create a mollie customer, create a mollie payment and store the payment in our database. 
        $payment = (new MolliePayment($customer, $contract, 'first'))->payment();

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
}
