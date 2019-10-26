<?php

namespace App\Models;

use App\Scopes\CustomerScope;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Location extends Model implements HasMedia
{
    use SoftDeletes, LogsActivity, HasMediaTrait;
    protected $fillable = ['name', 'facility_name'];

    protected static $logName = 'systeem';

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CustomerScope);
        self::creating(function ($model) {
            $model->customer_id = Customer::current()->id;
        });
    }
    
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Locatie {$eventName}";
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}