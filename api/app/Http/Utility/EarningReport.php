<?php


namespace App\Http\Utility;


class EarningReport {

    private static $earnings;
    private static $gameImage;
    private static $gameName;
    private static $participantCount;
    private static $tournamentId;
    private static $startDate;
    private static $earningPercentage;

    public function __construct($parameters = null) {
        self::setEarnings($parameters[EARNINGS]);
        self::setGameImage($parameters[GAME_IMAGE]);
        self::setGameName($parameters[GAME_NAME]);
        self::setParticipantCount($parameters[PARTICIPANT_COUNT]);
        self::setTournamentId($parameters[TOURNAMENT_ID]);
        self::setStartDate($parameters[START_DATE]);
        self::setEarningPercentage($parameters[EARNING_PERCENTAGE]);
    }

    public static function get() {
        return array(
            EARNINGS => self::getEarnings(),
            GAME_IMAGE => self::getGameImage(),
            GAME_NAME => self::getGameName(),
            PARTICIPANT_COUNT => self::getParticipantCount(),
            TOURNAMENT_ID => self::getTournamentId(),
            START_DATE => self::getStartDate(),
            EARNING_PERCENTAGE => self::getEarningPercentage()
        );
    }

    public static function getEarnings() {  return self::$earnings; }

    public static function setEarnings($earnings): void {  self::$earnings = $earnings; }

    public static function getGameImage() { return self::$gameImage; }

    public static function setGameImage($gameImage): void { self::$gameImage = $gameImage; }

    public static function getGameName() { return self::$gameName; }

    public static function setGameName($gameName): void { self::$gameName = $gameName; }

    public static function getParticipantCount() { return self::$participantCount; }

    public static function setParticipantCount($participantCount): void { self::$participantCount = $participantCount; }

    public static function getTournamentId() { return self::$tournamentId; }

    public static function setTournamentId($tournamentId): void { self::$tournamentId = $tournamentId; }

    public static function getStartDate() { return self::$startDate; }

    public static function setStartDate($startDate): void { self::$startDate = intval($startDate); }

    public static function getEarningPercentage() { return self::$earningPercentage; }

    public static function setEarningPercentage($earningPercentage): void { self::$earningPercentage = $earningPercentage; }
 }
