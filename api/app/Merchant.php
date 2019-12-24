<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model {

    protected $table = DB_MERCHANT_TABLE;
    protected $primaryKey = MERCHANT_ID;

    protected $fillable = [
        MERCHANT_ID, MERCHANT_TYPE, MERCHANT_KEY, IDENTITY_NUMBER, TAX_OFFICE, COMPANY_TITLE, IBAN
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
