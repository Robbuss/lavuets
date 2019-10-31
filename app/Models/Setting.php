<?php

namespace App\Models;

use App\Scopes\CustomerScope;
use Spatie\Activitylog\Traits\LogsActivity;

class Setting extends BaseModel
{
    use LogsActivity;
    protected $fillable = ['customer_id' ,'key', 'value'];
    
}
