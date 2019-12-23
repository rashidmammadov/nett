<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 07.03.2019
 * Time: 14:50
 */

namespace App\Http\Utility;

use App\Http\Queries\MySQL\ApiQuery;

class Match {

    private static $matchId;
    private static $tourId;
    private static $home;
    private static $away;
    private static $winner;
    private static $loser;
    private static $date;
    private static $available;

    public function __construct($parameters = null) {
        self::setMatchId($parameters[MATCH_ID]);
        self::setTourId($parameters[TOUR_ID]);
        self::setHome($parameters[HOME]);
        self::setAway($parameters[AWAY]);
        self::setWinner($parameters[WINNER]);
        self::setLoser($parameters[LOSER]);
        self::setDate($parameters[DATE]);
        self::setAvailable($parameters[AVAILABLE]);
    }

    public static function get() {
        return array(
            MATCH_ID => self::getMatchId(),
            TOUR_ID => self::getTourId(),
            HOME => self::getHome(),
            AWAY => self::getAway(),
            WINNER => self::getWinner(),
            LOSER => self::getLoser(),
            DATE => self::getDate(),
            AVAILABLE => self::getAvailable()
        );
    }

    public static function getMatchId() { return self::$matchId; }

    public static function setMatchId($matchId) { self::$matchId = $matchId; }

    public static function getTourId() { return self::$tourId; }

    public static function setTourId($tourId) { self::$tourId = $tourId; }

    public static function getHome() { return self::$home; }

    public static function setHome($home) { self::$home = $home; }

    public static function getAway() { return self::$away; }

    public static function setAway($away) { self::$away = $away; }

    public static function getWinner() { return self::$winner; }

    public static function setWinner($winner) { self::$winner = $winner; }

    public static function getLoser() { return self::$loser; }

    public static function setLoser($loser) { self::$loser = $loser; }

    public static function getDate() { return self::$date; }

    public static function setDate($date) { self::$date = $date; }

    public static function getAvailable() { return self::$available; }

    public static function setAvailable($available) { self::$available = $available; }

    public static function isSemiFinal($tourName) { return $tourName == SEMI_FINAL ? true : false; }

    public static function isThirdPlace($tourName) { return $tourName == THIRD_PLACE ? true : false; }

    public static function isFinal($tourName) { return $tourName == FINAL_ ? true : false; }

    public static function getMatchData($tourId, $tourName, $homeId, $awayId, $available, $date) {
        return array(
            TOUR_ID => $tourId,
            TOUR_NAME => $tourName,
            HOME_ID => $homeId,
            AWAY_ID => $awayId,
            AVAILABLE => $available,
            DATE => $date
        );
    }

    public static function prepareMatchParticipantData($participantId, $username, $picture, $point) {
        return array(
            PARTICIPANT_ID => $participantId,
            USERNAME => $username,
            PICTURE => $picture,
            POINT => $point
        );
    }

    /**
     * @description handle request to set winner of match.
     * @param $matchId
     * @param $homeId
     * @param $awayId
     * @param $homePoint
     * @param $awayPoint
     * @param string $note - holds the extra info about match.
     * @return array
     */
    public static function setMatchWinner($matchId, $homeId, $awayId, $homePoint, $awayPoint, $note): array {
        $winnerId = null;
        $loserId = null;
        if ($homePoint >= 0 && $awayPoint >= 0 && $homePoint != $awayPoint) {
            if ($homePoint > $awayPoint) {
                $winnerId = $homeId;
                $loserId = $awayId;
            } else if ($homePoint < $awayPoint) {
                $winnerId = $awayId;
                $loserId = $homeId;
            }
            ApiQuery::setMatchWinner($matchId, $winnerId, $loserId, $homePoint, $awayPoint, $note);
        }
        return array($winnerId, $loserId);
    }

    /**
     * @description set next tour match.
     * @param $tournamentId
     * @param $tourId
     * @param $tourName
     * @param $loserId
     * @param $winnerId
     * @param $date
     */
    public static function setNextTourMatch($tournamentId, $tourId, $tourName, $loserId, $winnerId, $date): void {
        if (Match::isSemiFinal($tourName)) {
            $thirdPlaceTourId = $tourId + 1;
            $finalTourId = $tourId + 2;
            $date = (double)$date + 3600000;
            self::updateNextTourMatchOpponents($tournamentId, $thirdPlaceTourId, $loserId, THIRD_PLACE, $date);
            self::updateNextTourMatchOpponents($tournamentId, $finalTourId, $winnerId, FINAL_, $date);
        } else if (!Match::isThirdPlace($tourName) && !Match::isFinal($tourName)) {
            $nextTourId = $tourId + 1;
            $nameArray = explode('/', $tourName);
            $tourName = $nameArray[1] ? ('1/' . (((int)$nameArray[1]) / 2)) : '';
            $date = (double)$date + 3600000;
            self::updateNextTourMatchOpponents($tournamentId, $nextTourId, $winnerId, $tourName, $date);
        }
    }

    /**
     * @description update match data to set opponents.
     * @param integer $tournamentId
     * @param integer $tourId
     * @param integer $participantId
     * @param string $tourName
     * @param $date
     */
    private static function updateNextTourMatchOpponents($tournamentId, int $tourId, $participantId,
                                                         string $tourName, $date = null): void {
        $match = ApiQuery::getNextMatch($tournamentId, $tourId);
        if ($match) {
            ApiQuery::updateMatchAway($match[MATCH_ID], $participantId);
        } else {
            ApiQuery::updateMatchHome($tournamentId, $tourId, $tourName, $date, $participantId);
        }
    }

}
