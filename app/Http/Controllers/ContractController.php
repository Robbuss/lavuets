<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Contract;
use App\Models\Customer;
use App\Utils\PdfGenerator;
use Illuminate\Http\Request;
use App\Utils\InvoiceGenerator;

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
                        'deactivated_at' => $q->deactivated_at,
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
                                'active' => $q->active,
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
                        'active' => $q->active,
                    ];
                }),
                'free' => Unit::doesntHave('contracts')->get()->map(function ($q) {
                    return [
                        'id' => $q->id,
                        'name' => $q->name,
                        'display_name' => $q->display_name,
                        'price' => $q->price,
                        'active' => $q->active,
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
        $contract = Contract::create(
            array_merge(
                $request->except(['units']),
                ['customer_id' => Customer::current()->id]
            )
        );
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
                'price' => $q->pivot->price,
                'active' => $q->active,
            ];
        });
        $units = Unit::doesntHave('contracts')->get()->map(function ($q) {
            return [
                'id' => $q->id,
                'name' => $q->name,
                'display_name' => $q->display_name,
                'price' => $q->price,
                'active' => $q->active,
            ];
        });

        return ['contract' => $contract, 'free_units' => $units];
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
        if ($request->free_units) {
            $contract->units()->detach();
        } else {
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
        $media = $contract->getMedia('pdf')->sortByDesc('created_at')->first();
        if (!$media) {
            $media = (new PdfGenerator($contract))->generateContract();
        }

        return [
            'content' => base64_encode(file_get_contents($contract->media()->first()->getPath())),
            'mime' => 'application/pdf',
            'extension' => 'pdf'
        ];
    }
}
