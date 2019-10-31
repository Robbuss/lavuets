<?php

namespace App\Models;

use App\Scopes\CustomerScope;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected static function boot()
    {
        parent::boot();
        if (!app()->runningInConsole()) {
            static::addGlobalScope(new CustomerScope);
        }
    }
}
