<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'price', 'size', 'x', 'y'];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    function activeContract()
    {
        return $this->hasOne(Contract::class)->where('active', 1);
    }
}
