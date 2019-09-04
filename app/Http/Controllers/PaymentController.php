<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index(){
        return Payment::all()->map(function($q){
            return [
                'customer' => $q->customer->name,  
                'payment_id' => $q->payment_id,  
                'mode' => $q->mode,
                'amount' => $q->amount,
                'status' => $q->status,  
                'created_at' => Carbon::parse($q->created_at)->isoFormat('LLL'),  
                'updated_at' => Carbon::parse($q->updated_at)->isoFormat('LLL'),  
            ];
        });
    }
}
