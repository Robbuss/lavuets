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
    public $customer;
    public $contract;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Customer $customer, Contract $contract)
    {
        $this->customer = $customer;
        $this->contract = $contract->with('units');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.bookingcomplete')->with([
            'customer' => $this->customer,
            'contract' => $this->contract
        ]);
    }
}
