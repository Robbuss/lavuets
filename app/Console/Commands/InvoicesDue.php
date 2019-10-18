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
        $contracts = Contract::whereNull('deactivated_at')->with(['invoices', 'customer'])->get();
        $count = 0;
        foreach ($contracts as $contract) {
            // get the last invoice on the contract
            $lastInvoice = $contract->invoices()->orderBy('end_date', 'DESC')->first();
            // if there is a last invoice and its end_date is in the past
            if ($lastInvoice && ($lastInvoice->end_date < Carbon::now())) {
                // create a new invoice
                $lastInvoice = (new InvoiceGenerator($contract, $lastInvoice))->lastInvoice;
                $count++;

                // charge the customers card/account via a mollie payment or create payment url
                if ($contract->customer->iban && $contract->payment_method === 'incasso') {
                    $type = 'first';
                    if ($contract->customer->mollie_id && $contract->customer->mandate_id) { // there already is a mandate && mollie customer
                        $type = 'recurring';
                    }
                    (new MolliePayment($contract->customer, $contract, $lastInvoice, $type))->payment();
                }
            }
        }
        activity('crontab')->log('Created ' . $count . ' new invoices');
    }
}
