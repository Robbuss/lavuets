<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MollieWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payment = Payment::where('payment_id', $request->id)->with(['customer', 'contract'])->firstOrFail();

        if ($payment->isPaid()) {

            // set the contract to active
            $payment->contract->update([
                'active' => true
            ]);

            // send a mail to the customer
            Mail::to($payment->customer->email)->bcc(config('mail.from.address'))->queue(new BookingComplete($payment));

            return ["success" => true];
        }
    }
}
