<?php

namespace App\Utils;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;

class PdfGenerator
{
    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function generate()
    {
        // make sure there is a directory for the customers' invoices
        Storage::disk('local')->makeDirectory('invoices/' . $this->invoice->customer_id);
        $filepath = storage_path('app/invoices/' . $this->invoice->customer_id . '/');
        $filename =  $this->invoice->ref . '.pdf';

        // view variables
        $this->invoice->load(['customer', 'contract']);
        $totalPrice = $this->totalPrice($this->invoice->contract);
        $pdf = PDF::loadView('invoice', ['invoice' => $this->invoice, 'total' => $totalPrice])->setOptions(['defaultFont' => 'sans-serif']);
        
        $pdf->save($filepath . $filename);

        $contents = file_get_contents($filepath . $filename);

        return [
            'content' => base64_encode($contents),
            'mime' => 'application/pdf',
            'extension' => 'pdf'
        ];
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
}
