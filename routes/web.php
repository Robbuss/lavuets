<?php
Route::post('/webhooks/mollie', 'MollieWebhookController@handle');
Route::post('/webhooks/mollie-first', 'MollieWebhookController@firstOrderWebhook');
Route::fallback(function() {
   return view('index'); // blade component with vue router init 
});