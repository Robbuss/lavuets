<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;
    protected $fillable = ['unit_id', 'customer_id', 'price', 'start_date', 'end_date'];
    // protected $dates = ['start_date', 'end_date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
