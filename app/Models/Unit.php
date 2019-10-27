<?php

namespace App\Models;

use App\Scopes\CustomerScope;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Unit extends Model implements HasMedia
{
    use SoftDeletes, HasMediaTrait;
    protected $fillable = ['location_id', 'name', 'price', 'size', 'active', 'x', 'y'];
    protected $appends = ['display_name'];
    protected $casts = [
        'active' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CustomerScope);
        self::creating(function ($model) {
            $model->customer_id = Customer::current()->id;
        });
    }
    
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

    function location()
    {
        return $this->belongsTo(Location::class);
    }
}
