<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Mollie\Laravel\Facades\Mollie;

class MollieWebhookController extends Controller
{
    public function handle(Request $request)
    {
        activity('mollie')->log('Mollie webhook post id ' . $request->id);
        // this code maybe removed since there is a cron job that runs essentially this code every five minutes
        $payment = Payment::where('payment_id', $request->id)->with(['customer', 'contract'])->firstOrFail();
        $molliePayment =  Mollie::api()->payments()->get($payment->payment_id);
        $payment->update(['status' => $molliePayment->status]);
    }
}
