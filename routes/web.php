<?php

use App\Models\Tenant;

// Route::get('/laravel/christmas', function() {
//    foreach(Tenant::all() as $tenant){
//       echo '<div style="padding:25px; width: 24%;display:inline-block">';
//       echo $tenant->name . '<br>';
//       echo $tenant->street_addr . ' ' . $tenant->street_number . '<br>' . $tenant->postal_code . ' ' . $tenant->city;
//       echo '</div>';
//    }
// });
Route::post('/webhooks/mollie', 'MollieWebhookController@handle');
Route::post('/webhooks/mollie-first', 'MollieWebhookController@firstOrderWebhook');
// Route::get('/laravel/webhooks/mollie-first', 'MollieWebhookController@firstOrderWebhook'); // for local testing purposes only
Route::fallback(function() {
   return view('index'); // blade component with vue router init 
});