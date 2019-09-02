<?php

namespace App\Http\Controllers;

use App\Utils\PdfGenerator;
use Illuminate\Http\Request;
use App\Mail\BookingComplete;
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Support\Facades\Mail;

class MollieWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payment = Payment::where('payment_id', $request->id)->with(['customer', 'contract'])->firstOrFail();
        
        $molliePayment =  Mollie::api()->payments()->get($payment->id);
        if ($molliePayment->isPaid()) {

            // generate a pdf contract
            new PdfGenerator($payment->contract);

            // send a mail to the customer
            Mail::to($payment->customer->email)->bcc(config('mail.from.address'))->queue(new BookingComplete($payment));

            return ["success" => true];
        }
    }
}
