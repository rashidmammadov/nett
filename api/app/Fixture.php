<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 11.03.2019
 * Time: 17:24
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Fixture extends Model {

    protected $table = DB_FIXTURE_TABLE;
    protected $primaryKey = TOURNAMENT_ID;

    protected $fillable = [
        TOURNAMENT_ID, FIXTURE
    ];

    public function tournament() {
        return $this->belongsTo('App\Tournament');
    }
}
