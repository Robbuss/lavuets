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
        return $this->view('emails.sendinvoice')->with(
            [
                'invoice' => $this->invoice,
                'payment_method' => $this->invoice->contract->payment_method,
            ])->attach($this->invoice->media()->first()->getPath(),
                ['mime' => 'application/pdf'],
        )->subject('Je nieuwe factuur');
    }
}
