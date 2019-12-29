<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    public function index(){
        return Activity::where('customer_id', Customer::current()->id)->get();
    }
}
