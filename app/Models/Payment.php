<?php

namespace App\Models;

use Carbon\Carbon;
use App\Scopes\CustomerScope;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends BaseModel
{
    use SoftDeletes, LogsActivity;
    protected $fillable = ['customer_id','tenant_id', 'contract_id', 'invoice_id', 'payment_id', 'status', 'mode', 'amount'];

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
