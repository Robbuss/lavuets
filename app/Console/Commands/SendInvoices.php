<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Mail\SendInvoice;
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
        if (count($invoicesDue) > 0) {
            // send invoices to mail 
            foreach ($invoicesDue as $invoice) {
                Mail::to($invoice->customer->email)
                ->bcc(config('mail.from.address'))
                ->queue(new SendInvoice($invoice));
                $invoice->update([
                    'sent' => true
                ]);
            }
        }
    }
}
