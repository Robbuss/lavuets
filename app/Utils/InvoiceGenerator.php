<?php

namespace App\Utils;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Contract;
use Carbon\CarbonPeriod;
use Symfony\Component\HttpFoundation\Request;

class InvoiceGenerator
{
    public $contract_id;
    public $note;

    public function __construct($contract_id, $note = null)
    {
        $this->contract_id = $contract_id;
    }

    public function generate()
    {
        $contract = Contract::findOrFail($this->contract_id);

        // check if its a full month and calculate the discount
        $endFirstMonth = Carbon::parse($contract->start_date)->modify('last day of this month');
        $daysFirstMonth =  Carbon::parse($contract->start_date)->daysInMonth;
        $remainingDays = Carbon::parse($contract->start_date)->diffInDays($endFirstMonth, false);
        $discount = round(($remainingDays / $daysFirstMonth) * 100);

        // add discount to the invoice
        $invoices = [
           [
            'start_date' => Carbon::parse($contract->start_date),
            'end_date' => $endFirstMonth,
            'days' => $remainingDays,
            'discount_pct' => $discount,
           ]
        ];

        // generate the invoices
        $i = 0;
        while($invoices[$i]['end_date'] < Carbon::parse($contract->end_date)){
            $start = Carbon::parse($invoices[$i]['start_date'])->addMonth()->modify('first day of this month');
            $end = Carbon::parse($invoices[$i]['start_date'])->addMonth()->modify('last day of this month');
            array_push($invoices, 
            [
                'start_date' => $start,
                'end_date' => $end,
                'days' => $start->daysInMonth,
                'discount_pct' => 0,
            ]);
            $i++;
        }

        // for each period, create an actual invoice
        foreach ($invoices as $invoice) {
            $i = Invoice::create([
                'ref' => 'Factuur-' . Carbon::parse($invoice['start_date'])->format('m-Y'),
                'contract_id' => $contract->id,
                'customer_id' => $contract->customer->id,
                'note' => ($this->note) ? $this->note : $contract->default_note,
                'payment_status' => 'unpaid',
                'start_date' => $invoice['start_date'],
                'end_date' => $invoice['end_date']
            ]);

        }
        return ["success" => true];
    }
}
