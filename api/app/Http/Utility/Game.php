<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 20.03.2019
 * Time: 17:01
 */

namespace App\Http\Utility;


class Game {

    private static $game = array(
        GAME_NAME => null,
        GAME_IMAGE => null,
        PLAYING_TYPE => null,
        PLATFORMS => null,
        DEVELOPER => null,
        GAME_TYPE => null
    );

    public function __construct($parameters = null) {
        !empty($parameters[GAME_NAME]) && self::setGameName($parameters[GAME_NAME]);
        !empty($parameters[GAME_IMAGE]) && self::setGameImage($parameters[GAME_IMAGE]);
        !empty($parameters[PLAYING_TYPE]) && self::setPlayingType($parameters[PLAYING_TYPE]);
        !empty($parameters[PLATFORMS]) && self::setPlatforms($parameters[PLATFORMS]);
        !empty($parameters[DEVELOPER]) && self::setDeveloper($parameters[DEVELOPER]);
        !empty($parameters[GAME_TYPE]) && self::setGameType($parameters[GAME_TYPE]);
    }

    public static function getGame() { return self::$game; }

    public static function getGameName() { return self::$game[GAME_NAME]; }

    public static function setGameName($value) { self::$game[GAME_NAME] = $value; }

    public static function getGameImage() { return self::$game[GAME_IMAGE]; }

    public static function setGameImage($value) { self::$game[GAME_IMAGE] = $value; }

    public static function getPlayingType() { return self::$game[PLAYING_TYPE]; }

    public static function setPlayingType($value) { self::$game[PLAYING_TYPE] = $value; }

    public static function getPlatforms() { return self::$game[PLATFORMS]; }

    public static function setPlatforms($value) { self::$game[PLATFORMS] = $value; }

    public static function getDeveloper() { return self::$game[DEVELOPER]; }

    public static function setDeveloper($value) { self::$game[DEVELOPER] = $value; }

    public static function getGameType() { return self::$game[GAME_TYPE]; }

    public static function setGameType($value) { self::$game[GAME_TYPE] = $value; }
}
