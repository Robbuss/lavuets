<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes, LogsActivity;
    protected $fillable = ['customer_id', 'deactivated_at', 'period', 'method', 'payment_method', 'auto_invoice', 'default_note', 'start_date'];
    protected $casts = [
        'auto_invoice' => 'boolean',
    ];
    protected $dates = [
        'start_date',
        'end_date',
        'deactivated_at'
    ];
    protected static $logName = 'systeem';

    public function getStartDateLocalizedAttribute()
    {
        return ($this->start_date) ? $this->start_date->isoFormat('LL') : null;
    }
    
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Contract {$eventName}";
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
