<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Payment;
use App\Utils\PdfGenerator;
use Illuminate\Http\Request;
use App\Mail\BookingComplete;
use Mollie\Laravel\Facades\Mollie;

class MollieWebhookController extends Controller
{
    /* this is the default mollie webhook; basicly only syncs payments status on changes */
    public function handle(Request $request)
    {
        activity('mollie')->log('Mollie webhook post id ' . $request->id);
        // this code maybe removed since there is a cron job that runs essentially this code every five minutes
        $payment = Payment::where('payment_id', $request->id)->with(['customer', 'contract'])->firstOrFail();
        $molliePayment =  Mollie::api()->payments()->get($payment->payment_id);
        $payment->update(['status' => $molliePayment->status]);
    }

    /* this method is called from the BookingController, and it sends the booking complete mail */
    public function firstOrderWebhook(Request $request) 
    {
        $payment = Payment::where('payment_id', $request->id)->with(['customer', 'contract'])->firstOrFail();
        $molliePayment =  Mollie::api()->payments()->get($payment->payment_id);
        $payment->update(['status' => $molliePayment->status]);
        if ($molliePayment->isPaid()) {
            $this->sendContractAndFirstInvoice($payment);
        }
    }

    private function sendContractAndFirstInvoice(Payment $payment)
    {
        // generate a pdf contract
        new PdfGenerator($payment->contract);

        // send a mail to the customer
        try {
            Mail::to($payment->customer->email)
                ->queue(new BookingComplete(
                    $payment->load(['customer', 'contract']),
                    storage_path('app/' . $payment->contract->customer_id . '/') . 'huurcontract-opslagmagazijn.pdf',
                    storage_path('app/' . $payment->contract->customer_id . '/') . $payment->invoice->ref . '.pdf',
                ));
            $payment->invoice->update([
                'sent' => Carbon::now(),
            ]);
            activity('email')->log('Welkomsmail en eerste factuur verstuurd naar' . $payment->customer->email);
        } catch (\Exception $e) {
            activity('email')->log('Something went wrong send mail');
        }

        return ["success" => true];
    }
}
