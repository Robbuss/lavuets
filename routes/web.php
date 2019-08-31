<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/invoice-preview', function(){
   $invoice = \App\Models\Invoice::first();
   return view('invoice', compact('invoice'));
});
Route::name('webhooks.mollie')->post('webhooks/mollie', 'MollieWebhookController@handle');
Route::fallback(function() {
   return view('index'); // blade component with vue router init 
});