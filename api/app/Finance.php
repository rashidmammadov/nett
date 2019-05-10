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
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        USER_ID, TYPE, CHANNEL, TOURNAMENT_ID, AMOUNT, TICKET, STATUS
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
