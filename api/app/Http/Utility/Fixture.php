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
use Illuminate\Support\Facades\Storage;
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
     * @description handle request to create new knock out fixture.
     * @param $startDate - holds the tournament`s start date
     * @param $days - holds the tournament`s total days.
     * @return array
     */
    public static function setKnockOutDraws($startDate, $days) {
        $matchCount = self::MAX_PARTICIPANT / 2;

        $draws = array();
        $counter = 0;
        while ($matchCount != 1) {
            $draw = self::setDefaultDraw($matchCount, (1 . '/' . $matchCount), $startDate, $counter);
            array_push($draws, $draw);
            $matchCount = $matchCount / 2;
            if ($matchCount == 1) {
                $thirdPlace = self::setDefaultDraw(1, '3th', $startDate, ($counter + 1));
                array_push($draws, $thirdPlace);
                $final = self::setDefaultDraw(1, 'final', $startDate, ($counter + 2));
                array_push($draws, $final);
            }
            $counter++;
            $startDate = $startDate + 3600000; // add one hour on each tour.
        }

        return $draws;
    }

    /**
     * @description handle request to set starting fixture draws.
     * @param $fixtureData - holds the fixture object.
     * @param $participants - holds the participants list of tournament.
     * @return mixed
     */
    public static function setKnockOutStartDraws($fixtureData, $participants) {
        $fixture = new Fixture($fixtureData);
        $draws = $fixture::getDraws();
        $drawIndex = 0;
        foreach ($draws as $draw) {
            if (count($draw[MATCHES]) >= count($participants)) {
                $drawIndex++;
            }
        }
        $firstDrawMatches = $draws[$drawIndex][MATCHES];

        /** set first draw matches **/
        $firstMatches = self::knockOutStartMatches($firstDrawMatches, $participants);
        $draws[$drawIndex][MATCHES] = $firstMatches;

        /** set second draw matches **/
        $secondDrawParticipants = array();
        for ($i = 0; $i < count($firstMatches); $i++) {
            $firstMatches[$i][WINNER] && array_push($secondDrawParticipants, $firstMatches[$i][WINNER]);
        }
        if (count($secondDrawParticipants) > 0) {
            $drawIndex = $drawIndex + 1;
            $secondDrawMatches = $draws[$drawIndex][MATCHES];
            $secondMatches = self::knockOutStartMatches($secondDrawMatches, $secondDrawParticipants);
            $draws[$drawIndex][MATCHES] = $secondMatches;
        }

        $fixture::setDraws($draws);
        return $fixture::getFixture();
    }

    /**
     * @description set next round player.
     * @param $draw - holds the given round data.
     * @param $player - holds the previous round winner.
     * @return mixed
     */
    public static function setKnockOutNextDraw($draw, $player) {
        if ($draw) {
            $matches = $draw[MATCHES];
            $findingOpponent = false;
            for ($i = 0; $i < count($matches); $i++) {
                if (!$findingOpponent) {
                    $matchObject = new Match($matches[$i]);
                    if (!$matches[$i][HOME] && !$matches[$i][AWAY]) {
                        $matchObject::setHome($player);
                        $matchObject::setWinner(null);
                        $matchObject::setAvailable(false);
                        $matchObject::setUpdatedAt(CustomDate::getDateFromMilliseconds());
                        $findingOpponent = true;
                        Log::info('SET HOME PLAYER', $matchObject::getHome());
                    } else if ($matches[$i][HOME] && !$matches[$i][AWAY]) {
                        $matchObject::setAway($player);
                        $matchObject::setWinner(null);
                        $matchObject::setAvailable(true);
                        $matchObject::setUpdatedAt(CustomDate::getDateFromMilliseconds());
                        $findingOpponent = true;
                        Log::info('SET AWAY PLAYER', $matchObject::getAway());
                    }
                    $draw[MATCHES][$i] = $matchObject::getMatch();
                }
            }
        }
        return $draw;
    }

    /**
     * @description set knock out tournament`s ranking.
     * @param $data - the data of fixture
     * @return mixed - ranking result
     */
    public static function setKnockOutRanking($data) {
        $fixture = new Fixture($data);
        $ranking = array();
        $participantCount = ApiQuery::getParticipants($fixture::getTournamentId())->count();
        $rankingDescIndex = 0;
        foreach ($fixture::getDraws() as $draw) {
            $players = array();
            foreach ($draw[MATCHES] as $match) {
                if (count($draw[MATCHES]) > 2) {
                    $loser = self::getPlayerMatchGoal($match, false);
                    $loser && array_push($players, $loser);
                } else if (count($draw[MATCHES]) == 1) {  /** if 3th place and final */
                    $winner = self::getPlayerMatchGoal($match, true);
                    $loser = self::getPlayerMatchGoal($match, false);
                    $winner && array_push($players, $winner);
                    $loser && array_push($players, $loser);
                }
            }

            /** sort participants by goal or point */
            usort($players, function ($a, $b) { return $a[GOAL] - $b[GOAL]; });
            for ($i = 0; $i < count($players); $i++) {
                $players[$i][TOURNAMENT_RANKING] = $participantCount - $rankingDescIndex;
                array_push($ranking, $players[$i]);
                $rankingDescIndex++;
            }
        }
        return $ranking;
    }

    public static function calculateKnockOutParticipantEarnings($standing, $participantCount) {
        $earning = 0;
        $ticket = 0;
        $result = (MAX_EARNINGS - (self::MAX_PARTICIPANT - $participantCount));
        if (intval($standing) == 1) {
            $earning = number_format($result, 2, '.', '');;
        } else if (intval($standing) == 2) {
            $earning = number_format($result / 2, 2, '.', '');;
        } else if (intval($standing) == 3) {
            $ticket = 1;
        }

        return array(
            EARNINGS => $earning,
            TICKET => $ticket
        );
    }

    public static function calculateKnockOutHolderEarning($participantCount) {
        $earning = $participantCount * MIN_AMOUNT;
        for ($i = 1; $i <= 3; $i++) {
            $result = self::calculateKnockOutParticipantEarnings($i, $participantCount);
            $earning -= $result[EARNINGS];
        }
        $earning = $earning - (MIN_AMOUNT + ($earning * COMMISSION_PERCENTAGE) / 100);
        return number_format($earning, 2, '.', '');
    }

    public static function set() {
        //Storage::disk('local')->put('fixtures/' . $fixture[TOURNAMENT_ID] . '.json', json_encode($fixture));
        //return $fixture;
    }

    /**
     * @description handle request to set starting matches of fixture draws.
     * @param $drawMatches - holds the given draw`s matches.
     * @param $participants - holds the participants list of tournament.
     * @return mixed
     */
    private static function knockOutStartMatches($drawMatches, $participants) {
        $participantTransformer = new ParticipantTransformer();
        $currentDate = new CustomDate();

        $matchObjectsArray = array();
        $homeIndex = 0;
        $awayIndex = $homeIndex + count($drawMatches);
        for ($i = 0; $i < count($drawMatches); $i++) {
            $matchObject = new Match($drawMatches[$i]);

            $homePlayer = $homeIndex < count($participants) ?
                $participantTransformer->transformForFixture($participants[$homeIndex]) : null;
            $awayPlayer = $awayIndex < count($participants) ?
                $participantTransformer->transformForFixture($participants[$awayIndex]) : null;

            $matchObject::setHome($homePlayer);
            $matchObject::setAway($awayPlayer);
            $matchObject::setAvailable(false);
            $matchObject::setUpdatedAt($currentDate::getDateFromMilliseconds());
            if (!is_null($homePlayer) && !is_null($awayPlayer)) {
                $matchObject::setAvailable(true);
                $matchObject::setWinner(null);
            } else if (!is_null($homePlayer) && is_null($awayPlayer)) {
                $matchObject::setWinner($homePlayer);
            }

            array_push($matchObjectsArray, $matchObject::getMatch());
            $homeIndex++;
            $awayIndex++;
        }
        return $matchObjectsArray;
    }

    private static function getPlayerMatchGoal($match, $winner) {
        $participantId = $winner ? $match[WINNER][PARTICIPANT_ID] : $match[LOSER][PARTICIPANT_ID];
        $point = $winner ? max($match[HOME][POINT], $match[AWAY][POINT]) : min($match[HOME][POINT], $match[AWAY][POINT]);
        $data = array(
            PARTICIPANT_ID => $participantId,
            GOAL => $point
        );

        if (!is_null($data[PARTICIPANT_ID]) && !is_null($data[GOAL])) {
            return $data;
        } else {
            return null;
        }
    }

    private static function setDefaultDraw($count, $title, $date, $tour) {
        $draw = array(
            DRAW_TITLE => $title,
            MATCHES => self::setDefaultMatches($count, CustomDate::getDateFromMilliseconds($date), $tour)
        );
        return $draw;
    }

    private static function setDefaultMatches($count, $date, $tour) {
        $matches = array();
        for ($i = 0; $i < $count; $i++) {
            $match = new Match(array(
                TOUR_ID => $tour,
                MATCH_ID => $i,
                UPDATED_AT => CustomDate::getDateFromMilliseconds()
            ));
            $match::setDate($date);
            array_push($matches, $match::getMatch());
        }
        return $matches;
    }
}
