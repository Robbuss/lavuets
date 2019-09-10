<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Payment;
use App\Utils\PdfGenerator;
use Illuminate\Http\Request;
use App\Mail\BookingComplete;
use App\Utils\InvoiceGenerator;
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Support\Facades\Mail;

class MollieWebhookController extends Controller
{
    public function handle(Request $request)
    {
        activity('mollie')->log('Mollie webhook post id ' . $request->id);
        
        $payment = Payment::where('payment_id', $request->id)->with(['customer', 'contract'])->firstOrFail();
        $molliePayment =  Mollie::api()->payments()->get($payment->payment_id);
        $payment->update(['status' => $molliePayment->status]);

        if ($molliePayment->isPaid()) {
            // generate a pdf contract
            new PdfGenerator($payment->contract);

            // generate an invoice
            $generator = new InvoiceGenerator($payment->contract);

            // send a mail to the customer
            try {
                Mail::to($payment->customer->email)
                    ->queue(new BookingComplete(
                        $payment->load(['customer', 'contract']),
                        storage_path('app/' . $payment->contract->customer_id . '/') . 'huurcontract-opslagmagazijn.pdf',
                        storage_path('app/' . $payment->contract->customer_id . '/') . $generator->lastInvoice->ref . '.pdf',
                    ));
                activity('email')->log('Welkomsmail en eerste factuur verstuurd naar' . $payment->customer->email);
            } catch (\Exception $e) {
                activity('email')->log('Something went wrong send mail');
            }

            $generator->lastInvoice->update([
                'sent' => Carbon::now(),
                'payment_id' => $payment->id
            ]);

            return ["success" => true];
        }
    }
}
