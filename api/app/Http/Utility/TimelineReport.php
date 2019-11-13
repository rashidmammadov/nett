<?php

namespace App\Http\Utility;

class TimelineReport {

    private static $gameImage;
    private static $gameName;
    private static $participantCount;
    private static $tournamentRanking;
    private static $tournamentId;
    private static $startDate;

    public function __construct($parameters = null) {
        !empty($parameters[GAME_IMAGE])         && self::setGameImage($parameters[GAME_IMAGE]);
        !empty($parameters[GAME_NAME])          && self::setGameName($parameters[GAME_NAME]);
        !empty($parameters[PARTICIPANT_COUNT])  && self::setParticipantCount($parameters[PARTICIPANT_COUNT]);
        !empty($parameters[TOURNAMENT_RANKING]) && self::setTournamentRanking($parameters[TOURNAMENT_RANKING]);
        !empty($parameters[TOURNAMENT_ID])      && self::setTournamentId($parameters[TOURNAMENT_ID]);
        !empty($parameters[START_DATE])         && self::setStartDate($parameters[START_DATE]);
    }

    public static function get() {
        return array(
            GAME_IMAGE => self::getGameImage(),
            GAME_NAME => self::getGameName(),
            PARTICIPANT_COUNT => self::getParticipantCount(),
            TOURNAMENT_RANKING => self::getTournamentRanking(),
            TOURNAMENT_ID => self::getTournamentId(),
            START_DATE => self::getStartDate()
        );
    }

    public static function getStartDate() { return self::$startDate; }

    public static function setStartDate($startDate): void { self::$startDate = $startDate; }

    public static function getGameImage() { return self::$gameImage; }

    public static function setGameImage($gameImage): void { self::$gameImage = $gameImage; }

    public static function getGameName() { return self::$gameName; }

    public static function setGameName($gameName): void { self::$gameName = $gameName; }

    public static function getParticipantCount() { return self::$participantCount; }

    public static function setParticipantCount($participantCount): void { self::$participantCount = $participantCount; }

    public static function getTournamentRanking() { return self::$tournamentRanking; }

    public static function setTournamentRanking($tournamentRanking): void { self::$tournamentRanking = $tournamentRanking; }

    public static function getTournamentId() { return self::$tournamentId; }

    public static function setTournamentId($tournamentId): void { self::$tournamentId = $tournamentId; }

}
