<?php
// Route::get('/invoice-preview', function(){
//    $invoice = \App\Models\Invoice::first();
//    return view('invoice', compact('invoice'));
// });
// Route::get('/contract-preview', function(){
//    $contract = App\Models\Contract::first();
//    (new PdfGenerator($contract));
// });
Route::post('/webhooks/mollie', 'MollieWebhookController@handle');
Route::get('/webhooks/mollie', 'MollieWebhookController@handle');

Route::fallback(function() {
   return view('index'); // blade component with vue router init 
});