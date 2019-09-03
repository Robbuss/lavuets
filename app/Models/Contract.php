<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes, LogsActivity;
    protected $fillable = ['customer_id', 'deactivated_at', 'period', 'method', 'payment_method', 'default_note', 'start_date'];
    protected $casts = [
        'auto_renew' => 'boolean',
    ];
    protected static $logName = 'systeem';
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

    public function scopeInvoicableContracts(){
        // make sure this returns the invoicableContracts; by check active and renew
    }
}
