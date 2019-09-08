<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Utils\PdfGenerator;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Invoice::with(['customer', 'contract', 'contract.units', 'payment'])
            ->where(function ($q) use ($request) {
                if ($request->customer_id)
                    return $q->where('customer_id', $request->customer_id);
            })
            ->get()->map(function ($q) {
                return [
                    'id' => $q->id,
                    'ref' => $q->ref,
                    'payment' => $q->payment ? $q->payment : ['payment_id' => false, 'status' => 'Geen id'],
                    'note' => $q->note,
                    'start_date' => $q->start_date_localized,
                    'end_date' => $q->end_date_localized,
                    'sent' => $q->sent,
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
        new PdfGenerator($invoice);
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
        $file = storage_path('app/' . $invoice->customer_id . '/') . $invoice->ref . '.pdf';
        if (file_exists($file))
            rename($file, storage_path('app/' . $invoice->customer_id . '/') . $invoice->ref . '-' . substr(md5(microtime()), -5) . '-deleted.pdf');
        $invoice->delete();

        return ['success' => true];
    }

    public function getPdf(Invoice $invoice)
    {
        $file = storage_path('app/' . $invoice->customer_id . '/') . $invoice->ref . '.pdf';
        if (!file_exists($file))
            return ["success" => false, "message" => "Er is geen Factuur gevonden gevonden..."];

        $content = file_get_contents($file);

        return [
            'content' => base64_encode($content),
            'mime' => 'application/pdf',
            'extension' => 'pdf'
        ];
    }
}
