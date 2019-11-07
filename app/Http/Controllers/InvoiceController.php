<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Contract;
use App\Models\Customer;
use App\Mail\SendInvoice;
use App\Utils\PdfGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Invoice::with(['credit', 'contract', 'units', 'contract.tenant', 'payments'])
            ->where(function ($q) use ($request) {
                if ($request->contract_id)
                    return $q->where('contract_id', $request->contract_id);
            })
            ->get()
            ->map(function ($q) {
                return [
                    'id' => $q->id,
                    'ref' => $q->ref,
                    'ref_number' => $q->ref_number,
                    'payments' => $q->payments ? $q->payments : ['payment_id' => false, 'status' => 'Geen id'],
                    'note' => $q->note,
                    'start_date' => $q->start_date,
                    'end_date' => $q->end_date,
                    'sent' => $q->sent,
                    'credit_invoice' => $q->credit,
                    'tenant' => [
                        'id' => $q->contract->tenant->id,
                        'name' => $q->contract->tenant->name,
                        'company_name' => $q->contract->tenant->company_name,
                        'email' => $q->contract->tenant->email,
                        'city' => $q->contract->tenant->city,
                        'street_addr' => $q->contract->tenant->street_addr,
                        'street_number' => $q->contract->tenant->street_number,
                        'postal_code' => $q->contract->tenant->postal_code,
                        'btw' => $q->contract->tenant->btw,
                        'kvk' => $q->contract->tenant->kvk
                    ],
                    'price' => $this->createDisplayPrice($q->units),
                    'units' => $q->units,
                    'contract_id' => $q->contract->id,
                ];
            });
    }

    private function createDisplayPrice($unitArray = [])
    {
        $priceArray = $unitArray->map(function ($q) {
            return $q->pivot->price;
        })->toArray();
        return array_sum($priceArray);
    }

    /**
     * Create a new invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $contract = Contract::findOrFail($request->contract_id);
        $invoice = Invoice::create([
            'customer_id' => Customer::current()->id,
            'ref' => $request->ref,
            'contract_id' => $contract->id,
            'credit_invoice' => $request->credit_invoice,
            'note' => $request->note,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);


        if ($request->credit_invoice) {
            $invoiceToCredit = Invoice::findOrFail($request->credit_invoice);
            $invoice->units()->sync($this->getSyncArray($invoiceToCredit->units, -1)); // send -1 value as price multiplier when credit_invoice
        } else {
            $invoice->units()->sync($this->getSyncArray($contract->units));
        }

        (new PdfGenerator($invoice))->generateInvoice();
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
        $invoice->update($request->except(['units']));
        $invoice->units()->sync($this->getSyncArray($request->units));
        (new PdfGenerator($invoice))->generateInvoice();

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

    public function send(Invoice $invoice)
    {
        // check if the file is there or create it
        $this->getPdf($invoice);
        // send invoice to the tenant
        try {
            Mail::to($invoice->contract->tenant->email)
                ->queue(new SendInvoice($invoice));
            activity('email')->log('Factuur verstuurd naar ' . $invoice->contract->tenant->email);
        } catch (\Exception $e) {
            activity('email')->log('Fout tijdens verzenden factuur naar ' . $invoice->contract->tenant->email);
        }

        // update the database so the invoice cannot be send again
        $invoice->update([
            'sent' => Carbon::now()
        ]);

        return ["success" => true, "message" => "Er is nog geen betaling aangemaakt."];
    }

    public function getPdf(Invoice $invoice)
    {
        $media = $invoice->getMedia('pdf')->sortByDesc('created_at')->first();
        if (!$media) {
            return ["success" => false, "message" => "Er ging iets mis met het ophalen van het bestand."];
        }
        return [
            'content' => base64_encode(file_get_contents($media->getPath())),
            'mime' => 'application/pdf',
            'extension' => 'pdf'
        ];
    }

    public function getSyncArray($priceArray, $credit = 1)
    {
        $unitPrice = [];
        foreach ($priceArray as $pu) {
            $unitPrice[$pu['id']] = ['price' => ($pu->pivot->price * $credit)];
        };
        return $unitPrice;
    }
}
