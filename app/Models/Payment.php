<?php

namespace App\Models;

use Carbon\Carbon;
use App\Scopes\CustomerScope;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes, LogsActivity;
    protected $fillable = ['tenant_id', 'contract_id', 'invoice_id', 'payment_id', 'status', 'mode', 'amount'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CustomerScope);
        self::creating(function ($model) {
            $model->customer_id = Customer::current()->id;
        });
    }
    
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
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
