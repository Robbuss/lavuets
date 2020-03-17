<?php

namespace App\Models;

use App\Traits\AssociateMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Unit extends BaseModel implements HasMedia
{
    use SoftDeletes, HasMediaTrait, AssociateMediaTrait;
    protected $fillable = ['customer_id', 'location_id', 'name', 'description', 'size_m3', 'size_m2', 'price', 'vat_percentage', 'show_frontend', 'should_tax', 'active'];
    protected $appends = ['display_name', 'thumb'];
    protected $casts = [
        'active' => 'boolean',
        'show_frontend' => 'boolean'
    ];
    

    public function getDisplayNameAttribute()
    {
        return $this->name . ' - €' . $this->price . ' - ' . $this->size_m3 . 'm3';
    }

    public function getThumbAttribute()
    {
        return $this->media->last() ? $this->media->last()->getUrl() : "/empty-location.svg";
    }

    public function contracts()
    {
        return $this->belongsToMany(Contract::class)->withPivot('price')->withTimestamps();
    }

    public function activeContract()
    {
        return $this->belongsToMany(Contract::class)->whereNull('deactivated_at');
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
