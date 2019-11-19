<?php

use Illuminate\Http\Request;

Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');

Route::get('/book-data/locations', 'BookingController@stepLocation');
Route::post('/book-data/units', 'BookingController@stepUnits');
Route::post('/booking/create', 'BookingController@create');
Route::get('/settings/layout', 'SettingController@layout');

Route::post('/customers/create', 'CustomerController@create');

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', 'AuthController@logout');
    Route::get('/user/index', 'UserController@index');
    Route::get('/user/profile', 'UserController@profile');

    Route::get('/locations', 'LocationController@index');
    Route::post('/locations/create', 'LocationController@create');
    Route::post('/locations/{location}', 'LocationController@update');
    Route::post('/locations/{location}/delete', 'LocationController@delete');

    Route::get('/units', 'UnitController@index');
    Route::get('/units/{unit}', 'UnitController@read');
    Route::post('/units/create', 'UnitController@create');
    Route::post('/units/{unit}', 'UnitController@update');
    Route::post('/units/{unit}/delete', 'UnitController@delete');

    Route::get('/tenants', 'TenantController@index');
    Route::get('/tenants/{tenant}', 'TenantController@read');
    Route::post('/tenants/create', 'TenantController@create');
    Route::post('/tenants/{tenant}', 'TenantController@update');
    Route::post('/tenants/{tenant}/delete', 'TenantController@delete');

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
    Route::post('/invoices/{invoice}/send', 'InvoiceController@send');

    Route::get('/logs', 'LogController@index');
    Route::get('/payments', 'PaymentsController@index');
    Route::post('/payments/{contract}/{invoice}/create', 'PaymentsController@create');
    Route::post('/payments/{payment}/related', 'PaymentsController@relatedPayments');

    Route::get('/dashboard', 'DashboardController@index');
    Route::get('/dashboard/unpaid-invoices', 'DashboardController@unpaidInvoices');
    Route::post('/dashboard/manual-tenant', 'Dashboardcontroller@manualTenant');

    Route::get('/settings', 'SettingController@index');
    Route::post('/settings', 'SettingController@update');
});
