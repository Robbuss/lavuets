<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Unit;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Contract;
use App\Models\Customer;
use Illuminate\Http\Request;
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

            'customers' => Customer::all(),
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
                'customer' => [
                    'name' => $q->customer->name,
                ],
                'payment_method' => $q->contract->payment_method,
                'sent' => $q->sent,
                'start_date' => $q->start_date,
                'end_date' => $q->end_date,
                'units' => $q->contract->units,
                'price' => $q->contract->units->sum('pivot.price'),
                'payments' => $q->payments ? $q->payments : ['payment_id' => false, 'status' => 'Geen id'],
            ];
        });
    }
}
