<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes, LogsActivity;
    protected $fillable = ['ref', 'contract_id', 'customer_id', 'payment_id', 'note', 'start_date', 'end_date', 'sent'];
    protected $dates = [
        'end_date',
        'start_date',
        'sent',
    ];
    public function getStartDateLocalizedAttribute()
    {
        return ($this->start_date) ? $this->start_date->isoFormat('LL'): null;
    }
    public function getEndDateLocalizedAttribute()
    {
        return ($this->end_date) ? $this->end_date->isoFormat('LL'): null;
    }    
    public function getSentLocalizedAttribute()
    {
        return ($this->sent) ? $this->sent->isoFormat('LL'): null;
    }   
 
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

    public function payment()
    {
        return $this->BelongsTo(Payment::class);
    }
}
