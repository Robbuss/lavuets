<?php

namespace App\Models;

use App\Scopes\CustomerScope;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Invoice extends BaseModel implements HasMedia
{
    use SoftDeletes, LogsActivity, HasMediaTrait;
    protected $fillable = ['customer_id', 'ref', 'credit_invoice', 'ref_number', 'contract_id', 'payment_id', 'note', 'start_date', 'end_date', 'sent'];
    protected $dates = [
        'end_date',
        'start_date',
        'sent',
    ];

    protected static $logName = 'systeem';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Factuur {$eventName}";
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function credit()
    {
        return $this->belongsTo(Invoice::class, 'credit_invoice');
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class)->withPivot('price')->withTimestamps();
    }

    /* Gets the invoices from an active contract, but without a payment with status pending or paid */
    public function scopeNotPaid($query)
    {
        return $query->with(['contract', 'contract.tenant', 'payments'])
            ->whereHas('contract', function ($q) {
                $q->whereNull('deactivated_at');
            })->whereHas('payments', function ($q) {
                $q->whereIn('status', ['expired', 'canceled']);
            })->whereDoesntHave('payments', function ($q) {
                $q->where('status', 'paid');
            });
    }

    /*
    * Gets the invoice without a payment
    * this result could be merged with scopeNotPaid
    */
    public function scopeWithoutPayment($query)
    {
        return $query->with(['contract', 'contract.tenant', 'payments'])
            ->whereHas('contract', function ($q) {
                $q->whereNull('deactivated_at');
            })->whereDoesntHave('payments');
    }
}
