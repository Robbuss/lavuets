<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Contract;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Mail\BookingComplete;
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
        $customer = Customer::create($request->customer);

        $contract = Contract::create(array_merge($request->contract, ['customer_id' => $customer->id]));
        $contract->units()->sync($this->getSyncArray($request->units));

        Mail::to($customer->email)->bcc(config('mail.from.address'))->queue(new BookingComplete($customer, $contract));

        return ["success" => true, 'redirect_url' => config('app.booking_complete_url')];
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
