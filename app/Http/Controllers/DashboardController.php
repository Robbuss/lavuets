<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Contract;
use App\Models\Customer;
use Illuminate\Http\Request;
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
                'occupied_active' => Unit::has('contracts')->where('active' , 1)->get()->count(),
                'free_active' => Unit::doesntHave('contracts')->where('active', 1)->get()->count(),
                'free_not_active' => Unit::doesntHave('contracts')->where('active' , 0)->get()->count(),
                'occupied_not_active' => Unit::has('contracts')->where('active' , 0)->get()->count(),
            ],
            'customers' => Customer::all(),

        ];
    }
}
