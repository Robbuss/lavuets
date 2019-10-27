<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Contract;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Utils\InvoiceGenerator;
use App\Utils\PdfGenerator;

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
            'contracts' => Contract::with(['tenant', 'units'])->get()->map(
                function ($q) {
                    return [
                        'id' => $q->id,
                        'tenant_id' => $q->tenant_id,
                        'tenant_name' => $q->tenant->name,
                        'company_name' => $q->tenant->company_name,
                        'deactivated_at' => ($q->deactivated_at) ? $q->deactivated_at->isoFormat('LL') : null,
                        'auto_invoice' => $q->auto_invoice,
                        'period' => $q->period,
                        'method' => $q->method,
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
                    ];
                }
            ),
            'units' => [
                'occupied' => Unit::has('contracts')->get()->map(function ($q) {
                    return [
                        'id' => $q->id,
                        'name' => $q->name,
                        'display_name' => $q->display_name,
                        'price' => $q->price,
                    ];
                }),
                'free' => Unit::doesntHave('contracts')->get()->map(function ($q) {
                    return [
                        'id' => $q->id,
                        'name' => $q->name,
                        'display_name' => $q->display_name,
                        'price' => $q->price
                    ];
                }),
            ],
            'tenants' => Tenant::all()
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
        // create the first invoice
        new InvoiceGenerator($contract);

        return ['id' => $contract->id];
    }

    public function read(Contract $contract)
    {
        $contract->load(['tenant', 'units']);
        $contract->units->map(function ($q) {
            return [
                'id' => $q->id,
                'name' => $q->name,
                'display_name' => $q->display_name,
                'price' => $q->pivot->price
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
        $contract->update($request->except(['units', 'freeUnits']));
        if($request->free_units){
            $contract->units()->detach();
        }else{
            $contract->units()->sync($this->getSyncArray($request->units));
        }

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
        $contract->units()->detach();
        $contract->delete();

        return ['success' => true];
    }

    public function getSyncArray($priceArray = [])
    {
        $contractUnitPrice = [];
        foreach ($priceArray as $pu) {
            $contractUnitPrice[$pu['id']] = ['price' => $pu['price']];
        };
        return $contractUnitPrice;
    }

    public function getPdf(Contract $contract)
    {
        $media = $contract->getFirstMedia('pdf');
        if (!$media) {
            $media = (new PdfGenerator($contract))->generateContract();
        }

        return [
            'content' => base64_encode(file_get_contents($media->getPath())),
            'mime' => 'application/pdf',
            'extension' => 'pdf'
        ];
    }
}
