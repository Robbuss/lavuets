<?php
Route::post('/webhooks/mollie', 'MollieWebhookController@handle');
Route::post('/webhooks/mollie-first', 'MollieWebhookController@firstOrderWebhook');
// Route::get('/laravel/webhooks/mollie-first', 'MollieWebhookController@firstOrderWebhook'); // for local testing purposes only
Route::fallback(function() {
   return view('index'); // blade component with vue router init 
});