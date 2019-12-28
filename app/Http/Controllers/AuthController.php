<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Setting;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\BadResponseException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        return $this->userAuth($request->email, $request->password);
    }

    public function singleSignOn(Request $request, $sso)
    {
        $user = User::where('id', $request->user_id)->first();
        if ($user->sso_token !== $sso) {
            abort(401);
        }
        return ['access_token' => $user->createToken('access_token')->accessToken];
    }

    public function userAuth($email, $password)
    {
        $http = new Client();
        try {
            $response = $http->post(Setting::where('key', 'login_endpoint')->first()->value, [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                    'username' => $email,
                    'password' => $password,
                ],
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
