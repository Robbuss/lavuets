<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\Contract;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingComplete extends Mailable
{
    use Queueable, SerializesModels;
    public $payment;
    public $pdf;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($payment, $contractPdf)
    {
        $this->payment = $payment;
        $this->pdf = $contractPdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.bookingcomplete')->with(['payment' => $this->payment])
            ->attach($this->pdf, [
                'as' => 'huurcontract.pdf',
                'mime' => 'application/pdf',
            ]);
            // ->attach('algemene/voorwaarden/path', [
            //     'as' => 'name.pdf',
            //     'mime' => 'application/pdf',
            // ]);
    }
}
