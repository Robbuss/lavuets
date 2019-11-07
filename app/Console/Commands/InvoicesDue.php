<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Contract;
use App\Utils\MolliePayment;
use App\Utils\InvoiceGenerator;
use Illuminate\Console\Command;

class InvoicesDue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:due';

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
        // first get all active contracts
        $contracts = Contract::whereNull('deactivated_at')->with(['invoices', 'tenant'])->get();
        $count = 0;
        foreach ($contracts as $contract) {
            // get the last invoice on the contract, that is not a credit invoice
            $lastInvoice = $contract->invoices()->whereNull('credit_invoice')->orderBy('end_date', 'DESC')->first();
            // if there is a last invoice and its end_date is in the past
            if (!$lastInvoice || ($lastInvoice->end_date < Carbon::now())) {
                // create a new invoice
                $lastInvoice = (new InvoiceGenerator($contract, $lastInvoice))->lastInvoice;
                $count++;
            }

        }
        activity('crontab')->log('Created ' . $count . ' new invoices');
    }
}
