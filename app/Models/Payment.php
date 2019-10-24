<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use SoftDeletes, LogsActivity;
    protected $fillable = ['customer_id', 'contract_id', 'invoice_id', 'payment_id', 'status', 'mode', 'amount'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
    
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
