<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Contract;
use App\Models\Customer;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return [
            'contracts' => Contract::with(['customer', 'units'])->get()->map(
                function ($q) {
                    return [
                        'id' => $q->id,
                        'customer_id' => $q->customer_id,
                        'customer_name' => $q->customer->name,
                        'active' => $q->active,
                        'payment_method' => $q->payment_method,
                        'units' => $q->units->map(function ($q) {
                            return [
                                'id' => $q->id,
                                'name' => $q->name,
                                'price' => $q->pivot->price,
                                'display_name' => $q->display_name,
                            ];
                        }),
                        'price' => $q->price,
                        'start_date' => $q->start_date,
                        'end_date' => $q->end_date
                    ];
                }
            ),
            'units' => Unit::all()->map(function ($q) {
                return [
                    'id' => $q->id,
                    'name' => $q->name,
                    'display_name' => $q->display_name,
                    'price' => $q->price
                ];
            }),
            'customers' => Customer::all()
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $contract = Contract::create($request->except(['units']));
        $contract->units()->sync($this->getSyncArray($request->units));

        return ['id' => $contract->id];
    }

    public function read(Contract $contract)
    {
        $contract->load(['customer', 'units']);
        $contract->units->map(function($q){
            return [
                'id' => $q->id,
                'name' => $q->name,
                'display_name' => $q->display_name,
                'price' => $q->pivot->price
            ];
        });
        $contract->allUnits = Unit::all()->map(function ($q) {
            return [
                'id' => $q->id,
                'name' => $q->name,
                'display_name' => $q->display_name,
                'price' => $q->price
            ];
        });
        return $contract;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contract $contract)
    {
        $contract->update($request->except(['units']));
        $contract->units()->sync($this->getSyncArray($request->units));

        return ['success' => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function delete(Contract $contract)
    {
        $contract->invoices()->delete();
        $contract->delete();

        return ['success' => true];
    }

    public function getSyncArray($priceArray = []){
        $contractUnitPrice = [];
        foreach ($priceArray as $pu) {
            $contractUnitPrice[$pu['id']] = ['price' => $pu['price']];
        };
        return $contractUnitPrice;
    }
}
