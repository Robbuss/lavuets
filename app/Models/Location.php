<?php

namespace App\Models;

use App\Scopes\CustomerScope;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Location extends BaseModel implements HasMedia
{
    use SoftDeletes, LogsActivity, HasMediaTrait;
    protected $fillable = ['customer_id' ,'name', 'facility_name'];

    protected static $logName = 'systeem';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Locatie {$eventName}";
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
