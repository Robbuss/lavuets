<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;
    protected $fillable = ['customer_id', 'contract_id', 'payment_id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
