<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;
    protected $fillable = ['customer_id', 'active', 'payment_method', 'default_note', 'start_date', 'end_date'];
    protected $casts = [
        'active' => 'boolean',
    ];

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
}
