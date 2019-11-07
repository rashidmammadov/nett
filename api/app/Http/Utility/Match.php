<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 07.03.2019
 * Time: 14:50
 */

namespace App\Http\Utility;


class Match {

    private static $match = array(
        TOUR_ID => null,
        MATCH_ID => null,
        AVAILABLE => false,
        HOME => null,
        AWAY => null,
        WINNER => null,
        LOSER => null,
        DATE => null,
        UPDATED_AT => null
    );

    public function __construct($parameters = null) {
        $parameters[TOUR_ID] >= 0 && self::setTourId($parameters[TOUR_ID]);
        $parameters[MATCH_ID] >= 0 && self::setMatchId($parameters[MATCH_ID]);
        !empty($parameters[AVAILABLE])  ? self::setAvailable($parameters[AVAILABLE]) : self::setAvailable(null);
        !empty($parameters[HOME])       ? self::setHome($parameters[HOME]) : self::setHome(null);
        !empty($parameters[AWAY])       ? self::setAway($parameters[AWAY]) : self::setAway(null);
        !empty($parameters[WINNER])     ? self::setWinner($parameters[WINNER]) : self::setWinner(null);
        !empty($parameters[LOSER])      ? self::setLoser($parameters[LOSER]) : self::setLoser(null);
        !empty($parameters[DATE])       ? self::setDate($parameters[DATE]) : self::setDate(null);
        !empty($parameters[UPDATED_AT]) ? self::setUpdatedAt($parameters[UPDATED_AT]) : self::setUpdatedAt(null);
    }

    public static function getMatch() { return self::$match; }

    public static function getTourId() { return self::$match[TOUR_ID]; }

    public static function setTourId($value) { self::$match[TOUR_ID] = $value; }

    public static function getMatchId() { return self::$match[MATCH_ID]; }

    public static function setMatchId($value) { self::$match[MATCH_ID] = $value; }

    public static function getAvailable() { return self::$match[AVAILABLE]; }

    public static function setAvailable($value) { self::$match[AVAILABLE] = $value; }

    public static function getHome() { return self::$match[HOME]; }

    public static function setHome($value) { self::$match[HOME] = $value; }

    public static function getAway() { return self::$match[AWAY]; }

    public static function setAway($value) { self::$match[AWAY] = $value; }

    public static function getWinner() { return self::$match[WINNER]; }

    public static function setWinner($value) { self::$match[WINNER] = $value; }

    public static function getLoser() { return self::$match[LOSER]; }

    public static function setLoser($value) { self::$match[LOSER] = $value; }

    public static function getDate() { return self::$match[DATE]; }

    public static function setDate($value) { self::$match[DATE] = $value; }

    public static function getUpdateAt() { return self::$match[UPDATED_AT]; }

    public static function setUpdatedAt($value) { self::$match[UPDATED_AT] = $value; }

    /**
     * @description handle request to set winner of match.
     * @param $data - holds the match data
     * @param $homePoint - the point of home player
     * @param $awayPoint - the point of away player
     * @return mixed
     */
    public static function setMatchWinner($data, $homePoint, $awayPoint) {
        $match = new Match($data);
        if ($homePoint >= 0 && $awayPoint >= 0 && $match::getAvailable() == true) {
            $home = $match::getHome();
            $away = $match::getAway();
            if ($home && $away) {
                if ($homePoint > $awayPoint) {
                    $match::setWinner($home);
                    $match::setLoser($away);
                } else if ($homePoint < $awayPoint) {
                    $match::setWinner($away);
                    $match::setLoser($home);
                }

                $home[POINT] = $homePoint;
                $away[POINT] = $awayPoint;
                $match::setHome($home);
                $match::setAway($away);
                $match::setUpdatedAt(CustomDate::getDateFromMilliseconds());
                $match::setAvailable(false);
            }
        }
        return $match::getMatch();
    }

}
