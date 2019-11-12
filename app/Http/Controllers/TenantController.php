<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tenant;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $tenant = Tenant::whereHas('contracts')->with('contracts')->get();
        return Tenant::all();
    }

    /**
     * Create a new tenant
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $tenant = Tenant::create([
            'customer_id'=> Customer::current()->id,
            'company_name' => $request->company_name,
            'name' => $request->name,
            'email' => $request->email,
            'city' => $request->city,
            'street_addr' => $request->street_addr,
            'street_number' => $request->street_number,
            'postal_code' => $request->postal_code,
            'is_company' => $request->is_company,
            'btw' => $request->btw,
            'kvk' => $request->kvk,
            'iban' => $request->iban,
            'phone' => $request->phone,
        ]);
        return ['id' => $tenant->id];
    }

    public function read(Tenant $tenant)
    {
        return $tenant;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tenant $tenant)
    {
        $tenant->update($request->all());
        return ['success' => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\Response
     */
    public function delete(Tenant $tenant)
    {
        $tenant->invoices()->delete();
        foreach ($tenant->contracts as $contract) {
            $contract->units()->detach();
            $contract->delete();
        }
        $tenant->payments()->delete();
        $tenant->delete();

        return ['success' => true];
    }
}
