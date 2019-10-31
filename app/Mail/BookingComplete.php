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
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($payment)
    {
        $this->payment = $payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.bookingcomplete')->with(['payment' => $this->payment])
            ->attach($this->payment->contract->getFirstMediaPath('pdf'), [
                'as' => 'huurcontract-opslagmagazijn.pdf',
                'mime' => 'application/pdf',
            ])
            ->attach(storage_path('app/public/'). 'algemene-voorwaarden-opslagmagazijn.pdf', [ // TODO: replace this by settings file
                'mime' => 'application/pdf',
            ])
            ->attach(storage_path('app/public/') . 'huisregels-opslagmagazijn.pdf', [ // TODO: replace this by settings file
                'mime' => 'application/pdf',
            ])
            ->attach($this->payment->invoice->getFirstMediaPath('pdf'), [
                'mime' => 'application/pdf',
            ])->subject('Welkom bij opslagmagazijn');
    }
}
