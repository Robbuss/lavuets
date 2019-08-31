<?php

namespace App\Mail;

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
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment->with(['customer', 'contract']);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.bookingcomplete')->with([
            'customer' => $this->payment->customer,
            'contract' => $this->payment->contract,
            'units' => $this->payment->contract->units,
        ]);
    }
}
