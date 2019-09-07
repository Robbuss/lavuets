<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'price', 'size', 'active', 'x', 'y'];
    protected $appends = ['display_name'];
    protected $casts = [
        'active' => 'boolean'
    ];
    public function getDisplayNameAttribute()
    {
        return $this->name . ' - €' . $this->price . ' - ' . $this->size . 'm3';
    }

    public function contracts()
    {
        return $this->belongsToMany(Contract::class)->withPivot('price')->withTimestamps();
    }

    function activeContract()
    {
        return $this->hasOne(Contract::class)->where('active', 1);
    }
}
