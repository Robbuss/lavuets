<?php

namespace App\Utils;

use App\Models\Invoice;
use App\Models\Contract;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;

class PdfGenerator
{
    public $model;
    public $filepath;
    public $filename;

    public function __construct($model)
    {
        $this->model = $model;
        $this->filepath = storage_path('app/');
    }

    public function generateInvoice()
    {
        $this->filename =  $this->model->ref . '.pdf';

        // view variables
        $this->model->load(['contract.tenant', 'contract']);
        $totalPrice = $this->totalPrice($this->model);

        $pdf = PDF::loadView('invoice', ['invoice' => $this->model, 'total' => $totalPrice])
            ->setOptions(['defaultFont' => 'sans-serif']);

        // save to disk
        $pdf->save($this->filepath . $this->filename);

        // save to the media library
        $this->model->addMedia($this->filepath . $this->filename)->toMediaCollection('pdf');
        activity('pdf')->log('Invoice PDF gemaakt');
    }

    public function generateContract()
    {
        $this->filename =  'huurcontract-opslagmagazijn.pdf';

        // view variables
        $units = [];
        $units['price'] = $this->model->units->sum('pivot.price');
        $units['units'] = implode(", ", $this->model->units->map(function ($q) {
            return $q->id;
        })->toArray());

        $pdf = PDF::loadView('contract', ['tenant' => $this->model->tenant, 'units' => $units, 'contract' => $this->model])
            ->setOptions(['defaultFont' => 'sans-serif']);

        // save to disk
        $pdf->save($this->filepath . $this->filename);

        // save to the media library
        $this->model->addMedia($this->filepath . $this->filename)->toMediaCollection('pdf');

        activity('pdf')->log('Contract PDF gemaakt');
    }

    public function totalPrice(Invoice $invoice)
    {
        $total = 0;
        foreach ($invoice->units as $unitPrice) {
            $total += $unitPrice->pivot->price;
        }
        return [
            'price' => $total,
            'btw' => $total * 0.21,
            'price_vat' => $total * 1.21
        ];
    }
}
