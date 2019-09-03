<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->view('emails.sendinvoice')->with([
            'invoice' => $this->invoice,
        ])->attach(
            storage_path('app/' . $this->invoice->customer_id . '/') . $this->invoice->ref . '.pdf',
            ['mime' => 'application/pdf'],
        )->subject('Je nieuwe factuur');
    }
}
