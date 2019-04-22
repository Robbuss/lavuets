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
            'contracts' => Contract::with(['customer', 'unit'])->get()->map(
                function ($q) {
                    return [
                        'id' => $q->id,
                        'customer_id' => $q->customer_id,
                        'customer_name' => $q->customer->name,
                        'unit_id' => $q->unit_id,
                        'unit_name' => $q->unit->name,
                        'price' => $q->price,
                        'start_date' => $q->start_date,
                        'end_date' => $q->end_date
                    ];
                }),
                'units' => Unit::all (
            ),
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
        $contract = Contract::create($request->all());

        return ['id' => $contract->id];
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
        $contract->update($request->all());
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
        $contract->delete();
        return ['success' => true];
    }
}
