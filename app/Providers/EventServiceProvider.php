<?php

namespace App\Providers;

use App\Mail\NewUser;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Invoice::creating(
            function ($contract) {
                $contract->ref_number = (Invoice::orderBy('id', 'DESC')->first()) ? Invoice::orderBy('id', 'DESC')->first()->ref_number + 1 : 1; // get the last invoice from the db and grab that number
            }
        );

        Customer::created(
            function ($customer) {
                Setting::createDefault($customer);
            }
        );
        //
    }
}
