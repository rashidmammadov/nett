<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 09.05.2019
 * Time: 17:42
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model {

    protected $table = DB_FINANCE_TABLE;
    protected $primaryKey = FINANCE_ID;

    protected $fillable = [
        FINANCE_ID, USER_ID, TYPE, CHANNEL, TOURNAMENT_ID, IBAN, AMOUNT, TICKET, STATUS
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
