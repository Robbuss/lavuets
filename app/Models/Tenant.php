<?php

namespace App\Models;

use App\Scopes\CustomerScope;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Tenant extends BaseModel implements HasMedia
{
    use SoftDeletes, LogsActivity, HasMediaTrait;

    protected $fillable = ['customer_id', 'mollie_id', 'mandate_id', 'company_name', 'name', 'email', 'city', 'street_addr', 'street_number', 'postal_code', 'phone', 'iban', 'is_company', 'btw', 'kvk'];
    protected static $logName = 'systeem';
    protected $casts = [
        'is_company' => 'boolean'
    ];

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
