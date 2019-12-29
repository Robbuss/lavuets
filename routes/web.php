<?php

use App\Models\Customer;

Route::post('/webhooks/mollie', 'MollieWebhookController@handle');
Route::post('/webhooks/mollie-first', 'MollieWebhookController@firstOrderWebhook');

// Route::get('/laravel/webhooks/mollie-first', 'MollieWebhookController@firstOrderWebhook'); // for local testing purposes only
Route::get('/images/logo.png', function () {
    $logo = Customer::current()->getMedia();
    if (count($logo) > 0) {
        return $logo->getFullUrl();
    }

    return 'logo.png';
});
Route::fallback(function () {
    return view('index'); // blade component with vue router init
});
