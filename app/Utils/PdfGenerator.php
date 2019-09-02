<?php

namespace App\Utils;

use App\Models\Invoice;
use App\Models\Contract;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;

class PdfGenerator
{
    public $model;
    private $filepath;
    private $filename;

    public function __construct($model)
    {
        $this->model = $model;
        Storage::disk('local')->makeDirectory($this->model->customer_id);
        $this->filepath = storage_path('app/' . $model->customer_id . '/');

        if ($model instanceof Invoice) {
            $this->generateInvoice();
        } else {
            $this->generateContract();
        }
    }

    public function generateInvoice()
    {
        $this->filename =  $this->model->ref . '.pdf';

        // view variables
        $this->model->load(['customer', 'contract']);
        $totalPrice = $this->totalPrice($this->model->contract);
        $pdf = PDF::loadView('invoice', ['invoice' => $this->model, 'total' => $totalPrice])
            ->setOptions(['defaultFont' => 'sans-serif']);

        $pdf->save($this->filepath . $this->filename);
    }

    public function generateContract()
    {
        $this->filename =  'huurcontract-opslagmagazijn.pdf';

        // view variables
        $units = [];
        $units['price'] = $this->model->units->sum('pivot.price');
        $units['units'] = implode($this->model->units->map(function ($q) {
            return $q->id;
        })->toArray(), ", ");

        $pdf = PDF::loadView('contract', ['customer' => $this->model->customer, 'units' => $units, 'contract' => $this->model])
            ->setOptions(['defaultFont' => 'sans-serif']);

        $pdf->save($this->filepath . $this->filename);
    }

    // Should make a different class or function for retrieving pdfs, instead of recreating them 
    // public function returnPdf()
    // {
    //     return [
    //         'content' => base64_encode(file_get_contents($this->filepath . $this->filename)),
    //         'mime' => 'application/pdf',
    //         'extension' => 'pdf'
    //     ];
    // }

    public function totalPrice(Contract $contract)
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
