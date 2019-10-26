<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $Customer = Customer::whereHas('contracts')->with('contracts')->get();
        return Customer::all();
    }

    /**
     * Create a new Customer
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $Customer = Customer::create([
            'domain' => $request->domain,
            'email' => $request->email,
            'name' => $request->name,
            'company_name' => $request->company_name,
        ]);
        return ['id' => $Customer->id];
    }

    public function read(Customer $Customer)
    {
        return $Customer;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $Customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $Customer)
    {
        $Customer->update($request->all());
        return ['success' => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $Customer
     * @return \Illuminate\Http\Response
     */
    public function delete(Customer $Customer)
    {
        $Customer->invoices()->delete();
        foreach ($Customer->contracts as $contract) {
            $contract->units()->detach();
            $contract->delete();
        }
        $Customer->payments()->delete();
        $Customer->delete();

        return ['success' => true];
    }
}
