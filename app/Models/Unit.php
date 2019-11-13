<?php

namespace App\Models;

use App\Scopes\CustomerScope;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Unit extends BaseModel implements HasMedia
{
    use SoftDeletes, HasMediaTrait;
    protected $fillable = ['customer_id', 'location_id', 'name', 'price', 'vat_percentage', 'show_frontend', 'should_tax', 'size', 'active', 'x', 'y'];
    protected $appends = ['display_name'];
    protected $casts = [
        'active' => 'boolean',
        'show_frontend' => 'boolean'
    ];

    public function getDisplayNameAttribute()
    {
        return $this->name . ' - €' . $this->price . ' - ' . $this->size . 'm3';
    }

    public function contracts()
    {
        return $this->belongsToMany(Contract::class)->withPivot('price')->withTimestamps();
    }

    public function activeContract()
    {
        return $this->hasOne(Contract::class)->where('active', 1);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function scopeBookableUnits()
    {
        return $this->doesntHave('contracts')->where('show_frontend', 1)->where('active', 1);
    }
}
