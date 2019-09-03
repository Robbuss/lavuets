<?php

namespace App\Utils;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Contract;
use Symfony\Component\HttpFoundation\Request;

class InvoiceGenerator
{
    public $contract;
    public $lastInvoice;
    public $note;

    public function __construct(Contract $contract, Invoice $lastInvoice = null, $note = null)
    {
        $this->contract = $contract;
        $this->lastInvoice = $lastInvoice;

        $this->generate();
    }

    private function generate()
    {
        // if there is a lastinvoice, use that date. Else use the contract start_date
        $date = $this->lastInvoice && $this->lastInvoice->end_date ? $this->lastInvoice->end_date : $this->contract->start_date;

        $newInvoice = Invoice::create([
            'ref' => 'Factuur-' . Carbon::parse($date)->format('m-Y'),
            'contract_id' => $this->contract->id,
            'customer_id' => $this->contract->customer->id,
            'note' => ($this->note) ? $this->note : $this->contract->default_note,
            'payment_id' => null,
            'start_date' => $date,
            'end_date' => Carbon::parse($date)->{$this->contract->method}($this->contract->period)
        ]);

        // generate a PDF for the invoice
        new PdfGenerator($newInvoice);
        $this->lastInvoice = $newInvoice;
    }
}
