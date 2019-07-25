<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Contract;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterval;
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

    public function totalPrice($contract)
    {
        $total = 0;
        foreach ($contract->units as $unitPrice) {
            $total += $unitPrice->pivot->price;
        }
        return [
            'price' => $total,
            'btw' => $total * 0.21,
        ];
    }

    public function generatePdf(Invoice $invoice)
    {
        $filepath = storage_path('app/invoices/' . $invoice->customer_id . '/');
        $filename =  $invoice->ref . '.pdf';
        $invoice->load(['customer', 'contract']);
        $totalPrice = $this->totalPrice($invoice->contract);
        $pdf = PDF::loadView('invoice', ['invoice' => $invoice, 'total' => $totalPrice])->setOptions(['defaultFont' => 'sans-serif']);
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

    public function generateInvoices(Request $request)
    {
        $contract = Contract::findOrFail($request->contract_id);

        // get the periods in an array
        $periods = CarbonPeriod::create($contract->start_date, '1 month', $contract->end_date)->toArray();

        // calculate from and end date per invoice
        $invoices = [];
        for ($i = 0; $i < 2; $i++) {
            if (array_key_exists($i + 1, $periods)) {
                $invoices[$i] = ['start_date' => $periods[$i], 'end_date' => $periods[$i + 1]];
            }
        }

        if (!$invoices)
            return ["error" => "Datum te kort om facruren te maken."];

        // dit kan later van pas komen voor facturen van de maand
        // $date = Carbon::now();
        // $date->addMonth();
        // $date->day = 0;
        // echo $date->toDateString(); // use toDateTimeString() to get date and time 
        // for each period, create an actual invoice
        foreach ($invoices as $invoice) {
            $i = Invoice::create([
                'ref' => 'Factuur-' . Carbon::parse($invoice['start_date'])->format('m-Y'),
                'contract_id' => $contract->id,
                'customer_id' => $contract->customer->id,
                'note' => ($request->note) ? $request->note : $contract->default_note,
                'payment_status' => 'unpaid',
                'start_date' => $invoice['start_date'],
                'end_date' => $invoice['end_date']
            ]);

            $this->generatePdf($i);
        }
        return ["success" => true];
    }
}
