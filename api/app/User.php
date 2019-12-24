<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        USERNAME, EMAIL, TYPE, PASSWORD, NAME, SURNAME, BIRTHDAY, SEX, PHONE, COUNTRY, CITY, DISTRICT, ADDRESS,
        GOOGLE_MAP_ADDRESS, PICTURE, BUDGET, PROMOTION, TICKET, RANKING, PREVIOUS_RANKING, ONESIGNAL_DEVICE_ID, STATE
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        PASSWORD, REMEMBER_TOKEN,
    ];

    public function finance() {
        return $this->hasMany('App\Finance');
    }

    public function merchant() {
        return $this->hasOne('App\Merchant');
    }

    public function tournament() {
        return $this->hasMany('App\Tournament');
    }
}
