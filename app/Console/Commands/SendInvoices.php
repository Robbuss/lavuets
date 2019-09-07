<?php

namespace App\Console\Commands;

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
        $invoicesDue = Invoice::where('sent', 0)->get();
        activity()->log('Running Crontab. Sending ' . count($invoicesDue). ' invoices');
        if (count($invoicesDue) > 0) {
            // send invoices to mail 
            foreach ($invoicesDue as $invoice) {
                // send invoice to the customer
                try{
                    Mail::to($invoice->customer->email)
                    ->bcc(config('mail.from.address'))
                    ->queue(new SendInvoice($invoice));
                    activity()->log('Factuur verstuurd naar '. $invoice->customer->email);
                }catch(\Exception $e){
                    activity()->log('Fout tijdens verzenden factuur naar '. $invoice->customer->email);
                }

                // update the database so the invoice cannot be send again
                $invoice->update([
                    'sent' => true
                ]);

                // charge the customers card/account via a mollie payment. 
                (new MolliePayment($invoice->customer, $invoice->contract, 'recurring'))->payment();
            }
        }
    }
}
