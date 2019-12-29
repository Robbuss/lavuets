<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Customer extends Model implements HasMedia
{
    use SoftDeletes, LogsActivity, HasMediaTrait;

    protected $fillable = ['domain', 'company_name', 'name', 'email', 'city','phone', 'street_addr', 'street_number', 'postal_code', 'phone', 'iban', 'btw', 'kvk'];
    protected static $logName = 'systeem';
    protected static $currentcustomer = null;

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Klant {$eventName}";
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    /**
     * Call this method to get current Customer
     *
     * @return Customer
     */
    public static function current($flush = false)
    {
        if (Customer::$currentcustomer === null) {
            $host = request()->getHost();
            Customer::$currentcustomer = Customer::where('domain', strtok($host, '.'))->first();
        }
        return Customer::$currentcustomer;
    }

    /**
     * Call this method to flush current Customer cache
     *
     * @return Customer
     */
    public static function flushCustomerCache()
    {
        Customer::$currentcustomer = null;
    }

    /**
     * Call this method to force current Customer cache
     *
     * @return Customer
     */
    public static function forceCustomerCache($customer)
    {
        Customer::$currentcustomer = $customer;
    }
}
