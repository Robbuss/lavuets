<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Mail\SendInvoice;
use App\Utils\MolliePayment;
use Illuminate\Console\Command;
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Support\Facades\Mail;

class SendInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all the invoices via mail that are overdue';

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
        // Get all the invoices that are not sent and belong to an active contract that allows auto_invoicing
        $invoicesDue = Invoice::whereNull('sent')->with('contract')->whereHas('contract', function ($q) {
            $q->where('auto_invoice', 1)->whereNull('deactivated_at')->where('start_date', '<', Carbon::now()); // with a contract start_date later than today
        })->get();
        // send invoices to mail 
        $count = 0;
        foreach ($invoicesDue as $invoice) {
            // send invoice to the tenant
            try {
                Mail::to($invoice->contract->tenant->email)
                    ->queue(new SendInvoice($invoice));
                activity('email')->log('Factuur verstuurd naar ' . $invoice->contract->tenant->email);
                // update the database so the invoice cannot be send again
                $invoice->update([
                    'sent' => Carbon::now()
                ]);
                $count++;
            } catch (\Exception $e) {
                activity('email')->log('Fout tijdens verzenden factuur naar ' . $invoice->contract->tenant->email);
            }
        }
        activity('crontab')->log('Unsend invoices:  ' . count($invoicesDue) . '. Sent: ' . $count);
    }
}
