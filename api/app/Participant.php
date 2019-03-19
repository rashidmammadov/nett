<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 12.03.2019
 * Time: 16:55
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model {

    protected $table = DB_PARTICIPANT_TABLE;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        TOURNAMENT_ID, PARTICIPANT_ID, PAYMENT_AMOUNT, PAYMENT_TYPE, EARNINGS, RANKING, POINT, REFERENCE_CODE
    ];

    public function tournament() {
        return $this->belongsTo('App\Tournament');
    }
}
