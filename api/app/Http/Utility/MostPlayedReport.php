<?php

namespace App\Http\Utility;

class MostPlayedReport {

    private static $gameId;
    private static $gameName;
    private static $gameImage;
    private static $totalCount;
    private static $activeStatusCount;
    private static $cancelStatusCount;
    private static $closeStatusCount;
    private static $openStatusCount;

    public function __construct($parameters = null) {
        !empty($parameters[GAME_ID])                && self::setGameId($parameters[GAME_ID]);
        !empty($parameters[GAME_NAME])              && self::setGameName($parameters[GAME_NAME]);
        !empty($parameters[GAME_IMAGE])             && self::setGameName($parameters[GAME_IMAGE]);
        !empty($parameters[TOTAL_COUNT])            && self::setTotalCount($parameters[TOTAL_COUNT]);
        self::setActiveStatusCount($parameters[ACTIVE_STATUS_COUNT]);
        self::setCancelStatusCount($parameters[CANCEL_STATUS_COUNT]);
        self::setCloseStatusCount($parameters[CLOSE_STATUS_COUNT]);
        !empty($parameters[OPEN_STATUS_COUNT])      && self::setOpenStatusCount($parameters[OPEN_STATUS_COUNT]);
    }

    public static function get() {
        return array(
            GAME_ID => self::getGameId(),
            GAME_NAME => self::getGameName(),
            GAME_IMAGE => self::getGameImage(),
            TOTAL_COUNT => self::getTotalCount(),
            ACTIVE_STATUS_COUNT => self::getActiveStatusCount(),
            CANCEL_STATUS_COUNT => self::getCancelStatusCount(),
            CLOSE_STATUS_COUNT => self::getCloseStatusCount(),
            OPEN_STATUS_COUNT => self::getOpenStatusCount()
        );
    }

    public static function getGameId() { return self::$gameId; }

    public static function setGameId($gameId): void { self::$gameId = $gameId; }

    public static function getGameName() { return self::$gameName; }

    public static function setGameName($gameName): void { self::$gameName = $gameName; }

    public static function getGameImage() { return self::$gameImage; }

    public static function setGameImage($gameImage): void { self::$gameImage = $gameImage; }

    public static function getTotalCount() { return self::$totalCount; }

    public static function setTotalCount($totalCount): void { self::$totalCount = $totalCount; }

    public static function getActiveStatusCount() { return self::$activeStatusCount; }

    public static function setActiveStatusCount($activeStatusCount): void { self::$activeStatusCount = $activeStatusCount; }

    public static function getCancelStatusCount() { return self::$cancelStatusCount; }

    public static function setCancelStatusCount($cancelStatusCount): void { self::$cancelStatusCount = $cancelStatusCount; }

    public static function getCloseStatusCount() { return self::$closeStatusCount; }

    public static function setCloseStatusCount($closeStatusCount): void { self::$closeStatusCount = $closeStatusCount; }

    public static function getOpenStatusCount() { return self::$openStatusCount; }

    public static function setOpenStatusCount($openStatusCount): void { self::$openStatusCount = $openStatusCount; }


}
