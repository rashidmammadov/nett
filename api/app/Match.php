<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Match extends Model {

    protected $table = DB_MATCH_TABLE;
    protected $primaryKey = MATCH_ID;

    protected $fillable = [
        MATCH_ID, TOURNAMENT_ID, TOUR_ID, TOUR_NAME, HOME_ID, AWAY_ID, HOME_POINT, AWAY_POINT, WINNER_ID, LOSER_ID, NOTE, AVAILABLE, DATE
    ];

    public function tournament() {
        return $this->belongsTo('App\Tournament');
    }
}
