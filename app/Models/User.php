<?php

namespace App\Models;

use App\Scopes\CustomerScope;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, Notifiable, HasMediaTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'sso_token', 'customer_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CustomerScope);
    }

    public function scopeCanLogin($query)
    {
        return $query->where('customer_id', Customer::current()->id);
    }

    public function tryLoginBySSO($token)
    {
        if ($this->sso_token !== null && Hash::check($token, $this->sso_token)) {
            return true;
        }
        return false;
    }

    public function regenerateSSOToken()
    {
        $token = str_random(32);
        $this->sso_token = Hash::make($token);
        $this->save();
        return $this->id.'_'.$token;
    }

    public static function ssoLogin($token) {
        $splitted = explode('_', $token, 2);
        if (count($splitted) !== 2 || !($user = User::canLogin()->whereId($splitted[0])->first()))
            return false;
        if ($user->tryLoginBySSO($splitted[1]))
            return $user;
        return false;
    }
}
