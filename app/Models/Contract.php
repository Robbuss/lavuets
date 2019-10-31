<?php

namespace App\Models;

use App\Scopes\CustomerScope;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Contract extends BaseModel implements HasMedia
{
    use SoftDeletes, LogsActivity, HasMediaTrait;
    protected $fillable = ['customer_id' , 'tenant_id', 'deactivated_at', 'period', 'method', 'payment_method', 'auto_invoice', 'default_note', 'start_date'];
    protected $casts = [
        'auto_invoice' => 'boolean',
    ];
    protected $dates = [
        'start_date',
        'end_date',
        'deactivated_at'
    ];
    protected static $logName = 'systeem';
   
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Contract {$eventName}";
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class)->withPivot('price')->withTimestamps();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeInvoicableContracts()
    {
        // make sure this returns the invoicableContracts; by check active and renew
    }
}
