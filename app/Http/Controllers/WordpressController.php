<?php

namespace App\Http\Controllers;

use App\Models\Customer;

class WordpressController extends Controller
{
    public function index(){
        return Customer::current();
    }
}
