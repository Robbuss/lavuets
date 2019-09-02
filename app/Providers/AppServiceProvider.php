<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         *  Set the locale to config app locale so that we can use:
         *  $carbon or DateTime obj ->formatLocalized('%A %d %B')
         *  anywhere in the applicantion
         */
        $ul = strtoupper(config('app.locale'));
        $ll = config('app.locale');
        if (config('app.env') === 'production') {
            $locale = $ll . '_' . $ul;
            setlocale(LC_TIME, $locale);
            setlocale(LC_ALL, $locale);
        } else {
            setlocale(LC_TIME, $ll);
            setlocale(LC_ALL, $ll);
        }
    }
}
