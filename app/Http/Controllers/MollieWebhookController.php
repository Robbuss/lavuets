<?php

namespace App\Http\Controllers;

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
        activity()->log('Mollie webhook post id ' . $request->id);
        $payment = Payment::where('payment_id', $request->id)->with(['customer', 'contract'])->firstOrFail();
        $molliePayment =  Mollie::api()->payments()->get($payment->payment_id);
        if ($molliePayment->isPaid()) {

            // generate a pdf contract
            new PdfGenerator($payment->contract);

            // generate an invoice
            $generator = new InvoiceGenerator($payment->contract);

            // send a mail to the customer
            try {
                Mail::to($payment->customer->email)
                    ->bcc(config('mail.from.address'))
                    ->queue(new BookingComplete(
                        $payment->load(['customer', 'contract']),
                        storage_path('app/' . $payment->contract->customer_id . '/') . 'huurcontract-opslagmagazijn.pdf',
                        storage_path('app/' . $payment->contract->customer_id . '/') . $generator->lastInvoice->ref . '.pdf',
                    ));
            } catch (\Exception $e) {
                activity()->log('Something went wrong send mail');
            }

            $generator->lastInvoice->update([
                'sent' => true,
                'payment_id' => $payment->id
            ]);

            return ["success" => true];
        }
    }
}
