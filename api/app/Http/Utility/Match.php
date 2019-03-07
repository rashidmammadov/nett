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
        AVAILABLE => false,
        HOME => null,
        AWAY => null,
        HOME_POINT => null,
        AWAY_POINT => null,
        WINNER => null,
        LOSER => null,
        DATE => null,
        UPDATED_AT => null
    );

    public function __construct($parameters = null) {
        !empty($parameters[AVAILABLE])  && self::setAvailable($parameters[AVAILABLE]);
        !empty($parameters[HOME])       && self::setHome($parameters[HOME]);
        !empty($parameters[AWAY])       && self::setAway($parameters[AWAY]);
        !empty($parameters[HOME_POINT]) && self::setAwayPoint($parameters[HOME_POINT]);
        !empty($parameters[AWAY_POINT]) && self::setAwayPoint($parameters[AWAY_POINT]);
        !empty($parameters[WINNER])     && self::setWinner($parameters[WINNER]);
        !empty($parameters[LOSER])      && self::setLoser($parameters[LOSER]);
        !empty($parameters[DATE])       && self::setDate($parameters[DATE]);
        !empty($parameters[UPDATED_AT]) && self::setUpdatedAt($parameters[UPDATED_AT]);
    }

    public static function getMatch() { return self::$match; }

    public static function getAvailable() { return self::$match[AVAILABLE]; }

    public static function setAvailable($value) { self::$match[AVAILABLE] = $value; }

    public static function getHome() { return self::$match[HOME]; }

    public static function setHome($value) { self::$match[HOME] = $value; }

    public static function getAway() { return self::$match[AWAY]; }

    public static function setAway($value) { self::$match[AWAY] = $value; }

    public static function getHomePoint() { return self::$match[HOME_POINT]; }

    public static function setHomePoint($value) { self::$match[HOME_POINT] = $value; }

    public static function getAwayPoint() { return self::$match[AWAY_POINT]; }

    public static function setAwayPoint($value) { self::$match[AWAY_POINT] = $value; }

    public static function getWinner() { return self::$match[WINNER]; }

    public static function setWinner($value) { self::$match[WINNER] = $value; }

    public static function getLoser() { return self::$match[LOSER]; }

    public static function setLoser($value) { self::$match[LOSER] = $value; }

    public static function getDate() { return self::$match[DATE]; }

    public static function setDate($value) { self::$match[DATE] = $value; }

    public static function getUpdateAt() { return self::$match[UPDATED_AT]; }

    public static function setUpdatedAt($value) { self::$match[UPDATED_AT] = $value; }
}
