<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Exception\BadResponseException;

class AuthController extends Controller
{
    public function login(Request $request){
        return $this->userAuth($request->email, $request->password);
    }

    public function singleSignOn(User $user, $sso)
    {
        if($user->sso_token !== $sso)
            abort(401);
        return $this->userAuth($user->email, $sso);   
    }

    public function userAuth($email, $password)
    {
        $http = new Client();
        try {
            $response = $http->post(config('services.passport.login_endpoint'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                    'username' => $email,
                    'password' => $password
                ]
            ]);
            activity('auth')->log($email . ' heeft ingelogd');
            return json_decode((string) $response->getBody(), true);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            return $responseBodyAsString;
        }
    }

    public function logout()
    {
        auth()->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json('Logged out. Bye', 200);
    }
}
