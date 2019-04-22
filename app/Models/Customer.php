<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    protected $fillable = ['company_name', 'name', 'email', 'city', 'street_addr', 'street_number', 'postal_code', 'btw', 'kvk'];
}