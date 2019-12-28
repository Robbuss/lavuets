<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        return User::all();
    }

    public function create(Request $request)
    {
        $user = User::create([
            'customer_id' => Customer::current()->id,
            'name' => $request->name,
            'password' => Hash::make(str_random(32)),
            'email' => $request->email,
        ]);
        $user->regenerateSSOToken();

        return ['success' => 'true'];
    }
}
