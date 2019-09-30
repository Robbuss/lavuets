<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes, LogsActivity;
    
    protected $fillable = ['mollie_id', 'mandate_id' ,'company_name', 'name', 'email', 'city', 'street_addr', 'street_number', 'postal_code', 'phone', 'iban', 'btw', 'kvk'];
    protected static $logName = 'systeem';
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Klant {$eventName}";
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