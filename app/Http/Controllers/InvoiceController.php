<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Invoice::with(['customer', 'contract', 'contract.unit'])->get()->map(function ($q) {
            return [
                'id' => $q->id,
                'ref' => $q->ref,
                'payment_status' => $q->payment_status,
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
                'unit' => [
                    'id' => $q->contract->unit->id,
                    'name' => $q->contract->unit->name,
                    'size' => $q->contract->unit->size,
                ],
                'contract' => [
                    'id' => $q->contract->id,
                    'price' => $q->contract->price,
                ]
            ];
        });
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
        $filepath = storage_path('app/invoices/' . $invoice->customer_id . '/');
        $filename =  $invoice->ref . '.pdf';
        $invoice->load(['customer', 'contract']);
        $pdf = PDF::loadView('invoice', ['invoice' => $invoice])->setOptions(['defaultFont' => 'sans-serif']);
        // $pdf->set_option('enable_css_float',true);
        // if (!file_exists($filepath . $filename)) {
            Storage::disk('local')->makeDirectory('invoices/' . $invoice->customer_id);
            $pdf->save($filepath . $filename);
        // }

        $contents = file_get_contents($filepath . $filename);

        return [
            'content' => base64_encode($contents),
            'mime' => 'application/pdf',
            'extension' => 'pdf'
        ];
    }
}
