<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Contract;
use App\Models\Customer;
use App\Utils\MolliePayment;
use Illuminate\Http\Request;
use App\Utils\InvoiceGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return [
            'user' => Auth::user(),
            'contracts' => Contract::with('units')->get()->map(
                function ($q) {
                    return [
                        'units' => $q->units->map(function ($q) {
                            return [
                                'id' => $q->id,
                                'name' => $q->name,
                                'price' => $q->pivot->price,
                                'display_name' => $q->display_name,
                            ];
                        }),
                    ];
                }
            ),
            'units' => [
                'free' => Unit::doesntHave('contracts')->get(),
                'occupied' => Unit::has('contracts')->get(),
                'occupied_active' => Unit::has('contracts')->where('active', 1)->get()->count(),
                'free_active' => Unit::doesntHave('contracts')->where('active', 1)->get()->count(),
                'free_not_active' => Unit::doesntHave('contracts')->where('active', 0)->get()->count(),
                'occupied_not_active' => Unit::has('contracts')->where('active', 0)->get()->count(),
            ],

            'tenants' => Tenant::all(),
            'payments' => Payment::where('created_at', '>=', Carbon::now()->subMonths(2))
                ->groupBy(['date', 'status'])
                ->orderBy('date', 'ASC')
                ->get([
                    DB::raw('Date(created_at) as date'),
                    DB::raw('status as status'),
                    DB::raw('SUM(amount) as "amount"')
                ]),
        ];
    }

    public function unpaidInvoices()
    {
        $invoicesWithoutPayment = Invoice::withoutPayment()->get();
        $invoices = $invoicesWithoutPayment->merge(Invoice::notPaid()->get());

        return $invoices->map(function ($q) {
            return [
                'id' => $q->id,
                'ref_number' => $q->ref_number,
                'contract_id' => $q->contract_id,
                'tenant' => [
                    'name' => $q->contract->tenant->name,
                ],
                'payment_method' => $q->contract->payment_method,
                'sent' => $q->sent,
                'start_date' => $q->start_date,
                'end_date' => $q->end_date,
                'units' => $q->units,
                'price' => $q->units->sum('pivot.price'),
                'payments' => $q->payments ? $q->payments : ['payment_id' => false, 'status' => 'Geen id'],
            ];
        });
    }

    public function manualTenant(Request $request)
    {
        // create a tenant
        $tenant = Tenant::create(
            array_merge(
                $request->tenant,
                ['customer_id' => Customer::current()->id]
            ),
        );

        // create a contract that is inactive till after payment is received (activated in the webhook)
        $contract = Contract::create(array_merge($request->contract, [
            'customer_id' => Customer::current()->id,
            'tenant_id' => $tenant->id,
            'auto_invoice' => true,
            'method' => 'addMonth',
            'payment_method' => 'incasso',
        ]));
        $contract->units()->sync($this->getSyncArray($request->units));

        // generate an invoice
        $invoice = new InvoiceGenerator($contract);

        // create a mollie customer, create a mollie payment and store the payment in our database. 
        if ($request->ideal) {
            $payments = (new MolliePayment($tenant, $contract, $invoice->lastInvoice))->payment();
            return ['success' => true, 'payment_url' => $payments['molliepayment']->getCheckoutUrl()];
        } else {
            // save the payment to our database
            Payment::create([
                'customer_id' => Customer::current()->id,
                'contract_id' => $contract->id,
                'tenant_id' => $tenant->id,
                'invoice_id' => $invoice->lastInvoice->id,
                'payment_id' => $request->payment['payment_id'],
                'status' => $request->payment['status'],
                'amount' => (string) $request->payment['price']
            ]);
        }

        // redirect tenant to Mollie checkout page
        return ['success' => true];
    }

    public function getSyncArray($priceArray = [])
    {
        $contractUnitPrice = [];
        foreach ($priceArray as $pu) {
            $contractUnitPrice[$pu['id']] = ['price' => $pu['price']];
        };

        return $contractUnitPrice;
    }
}
