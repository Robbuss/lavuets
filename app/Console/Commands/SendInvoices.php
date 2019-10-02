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
        $invoicesDue = Invoice::whereNull('sent')->with('contract')->whereHas('contract', function($q){
            $q->where('auto_invoice', 1)->whereNull('deactivated_at');
        })->get(); 

        if (count($invoicesDue) > 0) {
            // send invoices to mail 
            foreach ($invoicesDue as $invoice) {

                // only charge the customers that use incasso for the moment
                if($invoice->contract->payment_method === 'incasso')
                    $payment = $this->paymentUrl($invoice);
                
                // send invoice to the customer
                try {
                    Mail::to($invoice->customer->email)
                        ->queue(new SendInvoice($invoice, $payment));
                    activity('email')->log('Factuur verstuurd naar ' . $invoice->customer->email);
                } catch (\Exception $e) {
                    activity('email')->log('Fout tijdens verzenden factuur naar ' . $invoice->customer->email);
                }

                // update the database so the invoice cannot be send again
                $invoice->update([
                    'sent' => Carbon::now()
                ]);
            }
        }
        activity('crontab')->log('Sent ' . count($invoicesDue) . ' invoices');
    }

    public function paymentUrl($invoice)
    {
        // charge the customers card/account via a mollie payment or create payment url
        $type = 'first';
        if ($invoice->customer->mollie_id && $invoice->customer->mandate_id) {
            $type = 'recurring';
        }
        return (new MolliePayment($invoice->customer, $invoice->contract, $type))->payment();
    }
}
