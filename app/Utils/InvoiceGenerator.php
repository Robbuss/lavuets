<?php

namespace App\Utils;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Contract;
use Carbon\CarbonPeriod;
use Symfony\Component\HttpFoundation\Request;

class InvoiceGenerator
{
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function generate()
    {
        $contract = Contract::findOrFail($this->request->contract_id);

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
                'note' => ($this->request->note) ? $this->request->note : $contract->default_note,
                'payment_status' => 'unpaid',
                'start_date' => $invoice['start_date'],
                'end_date' => $invoice['end_date']
            ]);

            // generate the Pdf to storage
            new PdfGenerator($i);
        }
        return ["success" => true];
    }
}
