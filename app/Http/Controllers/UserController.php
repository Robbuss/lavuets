<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Mail\NewUser;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        return User::where('customer_id', Customer::current()->id,)->get();
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

        try{
            Mail::to($user->email)->queue(new NewUser($user));
        }catch(Exception $e){
            //
        }
        
        return ['success' => 'true'];
    }
}
