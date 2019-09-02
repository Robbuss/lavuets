<?php

use App\Utils\PdfGenerator;
use Carbon\Carbon;

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
Route::get('/contract-preview', function(){
   $contract = App\Models\Invoice::first();
   return (new PdfGenerator($contract))->returnPdf();
});
Route::post('/webhooks/mollie', 'MollieWebhookController@handle');

Route::fallback(function() {
   return view('index'); // blade component with vue router init 
});