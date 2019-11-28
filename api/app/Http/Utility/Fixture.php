<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 06.03.2019
 * Time: 15:57
 */

namespace App\Http\Utility;

use App\Http\Queries\MySQL\ApiQuery;
use Illuminate\Support\Facades\Log;
use App\Repository\Transformers\ParticipantTransformer;

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
        self::setTournamentId($parameters[TOURNAMENT_ID]);
        self::setHolderId($parameters[HOLDER_ID]);
        self::setGameId($parameters[GAME_ID]);
        self::setTournamentType($parameters[TOURNAMENT_TYPE]);
        self::setParticipantCount($parameters[PARTICIPANT_COUNT]);
        self::setCreatedAt($parameters[CREATED_AT]);
        self::setDraws($parameters[DRAWS]);
    }

    public static function get() { return self::$fixture; }

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
     * @description: prepare tournament`s fixture data.
     * @param integer $tournamentId
     * @return mixed
     */
    public static function prepareTournamentFixtureData($tournamentId) {
        $fixture = new Fixture();
        $fixtureDraws = array();
        $participants = ApiQuery::getParticipants($tournamentId);
        $draws = ApiQuery::getMatches($tournamentId);
        foreach ($draws as $matches) {
            $item = null;
            $drawMatches = array();
            foreach ($matches as $match) {
                $drawMatch = new Match($match);
                foreach($participants as $participant) {
                    if ($participant[IDENTIFIER] == $match[HOME_ID]) {
                        $home = Match::prepareMatchParticipantData($participant[IDENTIFIER], $participant[USERNAME],
                            $participant[PICTURE], $match[HOME_POINT]);
                        $drawMatch::setHome($home);
                        if ($match[HOME_ID] == $match[WINNER_ID]) {
                            $drawMatch::setWinner($home);
                        } else if ($match[HOME_ID] == $match[LOSER_ID]) {
                            $drawMatch::setLoser($home);
                        } else {
                            $drawMatch::setWinner(null);
                            $drawMatch::setLoser(null);
                        }
                    } else if ($participant[IDENTIFIER] == $match[AWAY_ID]) {
                        $away = Match::prepareMatchParticipantData($participant[IDENTIFIER], $participant[USERNAME],
                            $participant[PICTURE], $match[AWAY_POINT]);
                        $drawMatch::setAway($away);
                        if ($match[AWAY_ID] == $match[WINNER_ID]) {
                            $drawMatch::setWinner($away);
                        } else if ($match[AWAY_ID] == $match[LOSER_ID]) {
                            $drawMatch::setLoser($away);
                        } else {
                            $drawMatch::setWinner(null);
                            $drawMatch::setLoser(null);
                        }
                    }
                }
                array_push($drawMatches, $drawMatch::get());
            }
            $fixtureDraw = array(
                DRAW_TITLE => $matches[0][TOUR_NAME],
                MATCHES => $drawMatches
            );
            array_push($fixtureDraws, $fixtureDraw);
        }
        $fixture::setDraws($fixtureDraws);
        return $fixture::get();
    }

    /**
     * @description handle request to set starting fixture draws.
     * @param integer $tournamentId - holds the tournament id which is started.
     * @param integer $date - holds the start date of tournament.
     * @param array $participants - holds the participants of tournament.
     */
    public static function setKnockOutStartDraws($tournamentId, $date, $participants) {
        $tourId = 0;
        $maxMatchCount = self::MAX_PARTICIPANT / 2;
        while ($maxMatchCount >= count($participants)) {
            $maxMatchCount = $maxMatchCount / 2;
        };
        $nextRoundMatchCount = $maxMatchCount / 2;
        $withoutOpponents = self::setDrawMatches($tournamentId, $tourId, $date, $maxMatchCount, $participants, false);
        if (count($withoutOpponents) > 0) {
            self::setDrawMatches($tournamentId, $tourId + 1, (double)$date + 3600000, $nextRoundMatchCount,
                $withoutOpponents, true);
        }
    }

    /**
     * @description set matches for draws.
     * @param integer $tournamentId - holds the tournament id which is started.
     * @param integer $tourId - holds the id of draw.
     * @param integer $date - holds the start date of tournament.
     * @param integer $matchCount - holds the match count of draw.
     * @param array $participants - holds the participants of tournament.
     * @param boolean $flag - the flag for continue.
     * @return array
     */
    private static function setDrawMatches($tournamentId, $tourId, $date, $matchCount, $participants, $flag) {
        $withoutOpponents = array();
        for ($i = 0; $i < $matchCount; $i++) {
            $homeId = $participants[$i][PARTICIPANT_ID];
            $awayId = null;
            if (($i + $matchCount) < count($participants)) {
                $awayId = $participants[$i + $matchCount][PARTICIPANT_ID];
            }
            $match = Match::getMatchData($tourId, ('1/' . $matchCount), $homeId, $awayId, true, $date);
            if ($homeId && $awayId) {
                ApiQuery::setMatch($tournamentId, $match);
            } else if ($homeId && $flag) {
                ApiQuery::setMatch($tournamentId, $match);
            } else if ($homeId && !$awayId && !$flag) {
                array_push($withoutOpponents, $participants[$i]);
            }
        }
        return $withoutOpponents;
    }

    /**
     * @description set knock out tournament`s ranking.
     * @param integer $tournamentId - the id of tournament.
     * @return mixed - ranking result
     */
    public static function setKnockOutRanking($tournamentId) {
        $participants = ApiQuery::getParticipantsListedWithoutRanking($tournamentId);
        $rankings = array();
        foreach ($participants as $index => $participant) {
            $standing = ($index + 1) + 4;
            $ranking = array(
                PARTICIPANT_ID => $participant[PARTICIPANT_ID],
                TOURNAMENT_RANKING => $standing
            );
            array_push($rankings, $ranking);
        }
        return $rankings;
    }

    public static function calculateKnockOutParticipantEarnings($standing, $participantCount): array {
        $amount = 0;
        $ticket = 0;
        $result = (MAX_EARNINGS - (self::MAX_PARTICIPANT - $participantCount));
        if (intval($standing) == 1) {
            $amount = number_format($result, 2, '.', '');;
        } else if (intval($standing) == 2) {
            $amount = number_format($result / 2, 2, '.', '');;
        } else if (intval($standing) == 3) {
            $ticket = 1;
        }
        return array($amount, $ticket);
    }

    public static function calculateKnockOutHolderEarning($participantCount) {
        $earning = $participantCount * MIN_AMOUNT;
        for ($standing = 1; $standing <= 3; $standing++) {
            list($amount, $ticket) = self::calculateKnockOutParticipantEarnings($standing, $participantCount);
            $earning -= $amount;
        }
        $earning = $earning - (MIN_AMOUNT + ($earning * COMMISSION_PERCENTAGE) / 100);
        return number_format($earning, 2, '.', '');
    }

}
