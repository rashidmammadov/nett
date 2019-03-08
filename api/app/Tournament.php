<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 08.03.2019
 * Time: 17:29
 */

namespace App;

use Illuminate\Database\Eloquent\Model;


class Tournament extends Model {

    protected $table = DB_TOURNAMENT_TABLE;
    protected $primaryKey = TOURNAMENT_ID;

    protected $fillable = [
        TOURNAMENT_ID, HOLDER_ID, GAME_ID, FIXTURE_ID, TOURNAMENT_TYPE, PARTICIPANT_COUNT, START_DATE, STATUS
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
