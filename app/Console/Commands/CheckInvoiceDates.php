<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Contract;
use App\Mail\SentInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckInvoiceDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check what invoices are overdue';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // get all the contract where auto renew is on contracts
        $contracts = Contract::invoicableContracts()->with('invoices')->get();

        // for each contract, check if a new invoice should be send. 
        $invoicesDue = [];
        foreach($contracts as $contract){
            // check the billing period for example (1 month)
            // if the date of the last invoice is further in the past than today minus the length of the period
            // create a new invoice and add it to invoicesDue
        }

        if(count($invoicesDue) > 0){
            // send invoices to mail 
            foreach($invoicesDue as $invoice){
                Mail::to($invoice->customer->email)->queue(new SentInvoice($invoice));
            }
        }

    }
}
