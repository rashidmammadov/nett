<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 20.03.2019
 * Time: 16:45
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Game extends Model {

    protected $table = DB_GAME_TABLE;
    protected $primaryKey = GAME_ID;

    protected $fillable = [
        GAME_ID, GAME_NAME, GAME_IMAGE, PLAYING_TYPE, PLATFORMS, DEVELOPER, GAME_TYPE
    ];

    public function tournament() {
        return $this->belongsTo('App\Tournament');
    }
}
