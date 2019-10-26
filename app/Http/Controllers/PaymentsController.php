<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Contract;
use App\Utils\MolliePayment;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function index()
    {
        return Payment::with('invoice')->get()->map(function ($q) {
            return [
                'id' => $q->id,
                'tenant' => $q->tenant->name,
                'contract_id' => $q->contract_id,
                'payment_id' => $q->payment_id,
                'invoice_id' => $q->invoice_id,
                'invoice_ref_number' => $q->invoice ? $q->invoice->ref_number : null,
                'mode' => $q->mode,
                'amount' => $q->amount,
                'status' => $q->status,
                'created_at' => $q->created_at,
                'updated_at' => $q->updated_at,
            ];
        });
    }

    public function create(Contract $contract, Invoice $invoice, Request $request)
    {
        // charge the tenants card/account via a mollie payment or create payment url
        if ($contract->tenant->iban && $request->payment_method === 'incasso') {
            return (new MolliePayment($contract->tenant, $contract, $invoice))->payment();
        }

        if ($request->payment_method === 'factuur') {
            // save the payment to our database
            $payment = Payment::create([
                'contract_id' => $contract->id,
                'tenant_id' => $contract->tenant->id,
                'invoice_id' => $invoice->id,
                'payment_id' => $request->payment_id,
                'status' => $request->status,
                'amount' => (string) $request->price
            ]);
            return ['success' => true];
        }
    }

    public function relatedPayments(Payment $payment)
    {
        return Payment::where('invoice_id', $payment->invoice_id)->get();
    }
}
