<?php

use App\Models\Customer;

Route::post('/webhooks/mollie', 'MollieWebhookController@handle');
Route::post('/webhooks/mollie-first', 'MollieWebhookController@firstOrderWebhook');

// Route::get('/laravel/webhooks/mollie-first', 'MollieWebhookController@firstOrderWebhook'); // for local testing purposes only
Route::get('/images/logo.png', function () {
    $customer = Customer::current();
    if ($customer)
        return $customer->logo;

    return '/logo.png';
});
Route::fallback(function () {
    return view('index'); // blade component with vue router init
});
