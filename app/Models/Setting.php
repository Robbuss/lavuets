<?php

namespace App\Models;

use App\Scopes\CustomerScope;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Setting extends Model
{
    use LogsActivity;
    protected $fillable = ['key', 'value'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CustomerScope);
        self::creating(function ($model) {
            $model->customer_id = Customer::current()->id;
        });
    }
    
}
