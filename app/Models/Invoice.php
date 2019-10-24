<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes, LogsActivity;
    protected $fillable = ['ref', 'ref_number', 'contract_id', 'customer_id', 'payment_id', 'note', 'start_date', 'end_date', 'sent'];
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /* Gets the invoices from an active contract, but without a payment with status pending or paid */
    public function scopeNotPaid($query)
    {
        return $query->with(['contract', 'customer', 'payments'])
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
        return $query->with(['contract', 'customer', 'payments'])
            ->whereHas('contract', function ($q) {
                $q->whereNull('deactivated_at');
            })->whereDoesntHave('payments');
    }
}
