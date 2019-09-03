<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');

Route::post('/book-data', 'BookingController@index');
Route::post('/booking/create', 'BookingController@create');

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', 'AuthController@logout');
    Route::get('/user/index', 'UserController@index');
    Route::get('/user/profile', 'UserController@profile');

    Route::get('/units', 'UnitController@index');
    Route::get('/units/{unit}', 'UnitController@read');
    Route::post('/units/create', 'UnitController@create');
    Route::post('/units/{unit}', 'UnitController@update');
    Route::post('/units/{unit}/delete', 'UnitController@delete');

    Route::get('/customers', 'CustomerController@index');
    Route::get('/customers/{customer}', 'CustomerController@read');
    Route::post('/customers/create', 'CustomerController@create');
    Route::post('/customers/{customer}', 'CustomerController@update');
    Route::post('/customers/{customer}/delete', 'CustomerController@delete');

    Route::get('/contracts', 'ContractController@index');
    Route::get('/contracts/{contract}', 'ContractController@read');
    Route::post('/contracts/create', 'ContractController@create');
    Route::post('/contracts/{contract}', 'ContractController@update');
    Route::post('/contracts/{contract}/delete', 'ContractController@delete');
    Route::post('/contracts/{contract}/pdf', 'ContractController@getPdf');

    Route::post('/invoices', 'InvoiceController@index');
    Route::post('/invoices/create', 'InvoiceController@create');
    Route::get('/invoices/{invoice}/pdf', 'InvoiceController@getPdf');
    Route::post('/invoices/{invoice}', 'InvoiceController@update');
    Route::post('/invoices/{invoice}/delete', 'InvoiceController@delete');    

    Route::get('/logs', 'LogController@index');    
});
