<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Utils\PdfGenerator;
use Illuminate\Http\Request;
use App\Utils\InvoiceGenerator;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Invoice::with(['customer', 'contract', 'contract.units'])
            ->where(function ($q) use ($request) {
                if ($request->customer_id)
                    return $q->where('customer_id', $request->customer_id);
            })
            ->get()->map(function ($q) {
                return [
                    'id' => $q->id,
                    'ref' => $q->ref,
                    'payment_status' => $q->payment_status,
                    'note' => $q->note,
                    'start_date' => $q->start_date,
                    'end_date' => $q->end_date,
                    'customer' => [
                        'id' => $q->customer->id,
                        'name' => $q->customer->name,
                        'company_name' => $q->customer->company_name,
                        'email' => $q->customer->email,
                        'city' => $q->customer->city,
                        'street_addr' => $q->customer->street_addr,
                        'street_number' => $q->customer->street_number,
                        'postal_code' => $q->customer->postal_code,
                        'btw' => $q->customer->btw,
                        'kvk' => $q->customer->kvk
                    ],
                    'contract' => [
                        'id' => $q->contract->id,
                        'price' => $this->createDisplayPrice($q->contract->units),
                        'units' => $q->contract->units
                    ]
                ];
            });
    }

    private function createDisplayPrice($unitArray = [])
    {
        $priceArray = $unitArray->map(function ($q) {
            return $q->pivot->price;
        })->toArray();
        return implode(', €', $priceArray) . ' Totaal €' . array_sum($priceArray);
    }

    /**
     * Create a new invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $invoice = Invoice::create($request->all());
        return ['id' => $invoice->id];
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        $invoice->update($request->all());
        return ['success' => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function delete(Invoice $invoice)
    {
        $invoice->delete();
        return ['success' => true];
    }

    public function generatePdf(Invoice $invoice)
    {
        return (new PdfGenerator($invoice))->generate();
    }

    public function generateInvoices(Request $request)
    {
        return (new InvoiceGenerator($request->contract_id, $request->note))->generate();
    }
}
