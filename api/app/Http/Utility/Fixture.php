<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 06.03.2019
 * Time: 15:57
 */

namespace App\Http\Utility;

use Illuminate\Support\Facades\Storage;

class Fixture {

    const MAX_PARTICIPANT = 32;

    private static $fixture = array(
        TOURNAMENT_ID => null,
        HOLDER_ID => null,
        GAME_ID => null,
        TOURNAMENT_TYPE => null,
        PARTICIPANT_COUNT => null,
        CREATED_AT => null,
        DRAWS => array()
    );

    public function __construct($parameters = null) {
        !empty($parameters[TOURNAMENT_ID]) && self::setTournamentId($parameters[TOURNAMENT_ID]);
        !empty($parameters[HOLDER_ID]) && self::setHolderId($parameters[HOLDER_ID]);
        !empty($parameters[GAME_ID]) && self::setGameId($parameters[GAME_ID]);
        !empty($parameters[TOURNAMENT_TYPE]) && self::setTournamentType($parameters[TOURNAMENT_TYPE]);
        !empty($parameters[PARTICIPANT_COUNT]) && self::setParticipantCount($parameters[PARTICIPANT_COUNT]);
        !empty($parameters[CREATED_AT]) && self::setCreatedAt($parameters[CREATED_AT]);
        !empty($parameters[DRAWS]) && self::setDraws($parameters[DRAWS]);
    }

    public static function getFixture() { return self::$fixture; }

    public static function getTournamentId() { return self::$fixture[TOURNAMENT_ID]; }

    public static function setTournamentId($value) { self::$fixture[TOURNAMENT_ID] = $value; }

    public static function getHolderId() { return self::$fixture[HOLDER_ID]; }

    public static function setHolderId($value) { self::$fixture[HOLDER_ID] = $value; }

    public static function getGameId() { return self::$fixture[GAME_ID]; }

    public static function setGameId($value) { self::$fixture[GAME_ID] = $value; }

    public static function getTournamentType() { return self::$fixture[TOURNAMENT_TYPE]; }

    public static function setTournamentType($value) { self::$fixture[TOURNAMENT_TYPE] = $value; }

    public static function getParticipantCount() { return self::$fixture[PARTICIPANT_COUNT]; }

    public static function setParticipantCount($value) { self::$fixture[PARTICIPANT_COUNT] = $value; }

    public static function getCreatedAt() { return self::$fixture[CREATED_AT]; }

    public static function setCreatedAt($value) { self::$fixture[CREATED_AT] = $value; }

    public static function getDraws() { return self::$fixture[DRAWS]; }

    public static function setDraws($value) { self::$fixture[DRAWS] = $value; }

    /**
     * @description handle request to create new user.
     * @param $startDate - holds the tournament`s start date
     * @param $days - holds the tournament`s total days.
     * @return array
     */
    public static function setKnockOutDraws($startDate, $days) {
        $matchCount = self::MAX_PARTICIPANT / 2;

        $draws = array();
        while ($matchCount != 1) {
            $draw = self::setDefaultDraw($matchCount, (1 . '/' . $matchCount), $startDate);
            array_push($draws, $draw);
            $matchCount = $matchCount / 2;
            if ($matchCount == 1) {
                $thirdPlace = self::setDefaultDraw(1, '3th', $startDate);
                array_push($draws, $thirdPlace);
                $final = self::setDefaultDraw(1, 'final', $startDate);
                array_push($draws, $final);
            }
        }

        return $draws;
    }

    public static function set() {

        //Storage::disk('local')->put('fixtures/' . $fixture[TOURNAMENT_ID] . '.json', json_encode($fixture));

        //return $fixture;
    }

    private static function setDefaultDraw($count, $title, $date) {
        $draw = array(
            DRAW_TITLE => $title,
            MATCHES => self::setDefaultMatches($count, CustomDate::getDateFromMilliseconds($date))
        );
        return $draw;
    }

    private static function setDefaultMatches($count, $date) {
        $matches = array();
        for ($i = 0; $i < $count; $i++) {
            $match = new Match(array(UPDATED_AT => CustomDate::getDateFromMilliseconds()));
            $match::setDate($date);
            array_push($matches, $match::getMatch());
        }
        return $matches;
    }
}
