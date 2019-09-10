<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Mail\SendInvoice;
use App\Utils\MolliePayment;
use Illuminate\Console\Command;
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
        $invoicesDue = Invoice::whereNull('sent')->get();
        $count = 0;
        if (count($invoicesDue) > 0) {
            // send invoices to mail 
            foreach ($invoicesDue as $invoice) {
                // check if the contract allows auto invoicing
                if (!$invoice->contract->auto_invoice || $invoice->contract->deactivated_at)
                    return;

                // charge the customers bankaccount or create a new payment url
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

                $count++;
            }
        }
        activity('crontab')->log('Sent ' . $count . ' invoices');
    }

    public function paymentUrl($invoice)
    {
        // charge the customers card/account via a mollie payment or create payment url
        $payment = null;
        $type = 'first';
        if ($invoice->customer->mollie_id) {
            $mollieCustomer = Mollie::api()->customers()->get($invoice->customer->mollie_id);
            $mandates = Mollie::api()->mandates()->listFor($mollieCustomer);
            if ($mollieCustomer && $mandates) {
                $type = 'recurring';
            }
        }
        return (new MolliePayment($invoice->customer, $invoice->contract, $type))->payment();
    }
}
