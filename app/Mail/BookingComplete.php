<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingComplete extends Mailable
{
    use Queueable, SerializesModels;
    public $payment;
    public $contractPdf;
    public $invoicePdf;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($payment, $contractPdf, $invoicePdf)
    {
        $this->payment = $payment;
        $this->contractPdf = $contractPdf;
        $this->invoicePdf = $invoicePdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.bookingcomplete')->with(['payment' => $this->payment])
            ->attach($this->contractPdf, [
                'as' => 'huurcontract-opslagmagazijn.pdf',
                'mime' => 'application/pdf',
            ])
            ->attach($this->invoicePdf, [
                'mime' => 'application/pdf',
            ])->subject('Welkom bij opslagmagazijn');
            // ->attach('algemene/voorwaarden/path', [
            //     'as' => 'name.pdf',
            //     'mime' => 'application/pdf',
            // ]);
    }
}
