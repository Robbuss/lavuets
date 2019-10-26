<?php

namespace App\Models;

use App\Scopes\CustomerScope;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Tenant extends Model implements HasMedia
{
    use SoftDeletes, LogsActivity, HasMediaTrait;

    protected $fillable = ['mollie_id', 'mandate_id', 'company_name', 'name', 'email', 'city', 'street_addr', 'street_number', 'postal_code', 'phone', 'iban', 'btw', 'kvk'];
    protected static $logName = 'systeem';

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CustomerScope);
        self::creating(function ($model) {
            $model->customer_id = Customer::current()->id;
        });
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Huurder {$eventName}";
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
