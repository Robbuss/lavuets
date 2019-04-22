<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Customer::all();
    }

    /**
     * Create a new customer
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // TODO: validate fields 
        $customer = Customer::create([
            'company_name' => $request->company_name,
            'name' => $request->name,
            'email' => $request->email,
            'city' => $request->city,
            'street_addr' => $request->street_addr,
            'street_number' => $request->street_number,
            'postal_code' => $request->postal_code,
            'btw' => $request->btw,
            'kvk' => $request->kvk,
        ]);
        return ['id' => $customer->id];
    }

    public function read(Customer $customer)
    {
        return $customer;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $customer->update($request->all());
        return ['success' => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function delete(Customer $customer)
    {
        $customer->delete();
        return ['sucess' => true];
    }
}
