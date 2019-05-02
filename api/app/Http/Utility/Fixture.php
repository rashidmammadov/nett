<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 06.03.2019
 * Time: 15:57
 */

namespace App\Http\Utility;

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
                $thirdPlace = self::setDefaultDraw(1, '3th', $startDate, $counter);
                array_push($draws, $thirdPlace);
                $final = self::setDefaultDraw(1, 'final', $startDate, $counter);
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
    private static function knockOutStartMatches($drawMatches, $participants)
    {
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

    private static function setDefaultDraw($count, $title, $date, $tour)
    {
        $draw = array(
            DRAW_TITLE => $title,
            MATCHES => self::setDefaultMatches($count, CustomDate::getDateFromMilliseconds($date), $tour)
        );
        return $draw;
    }

    private static function setDefaultMatches($count, $date, $tour)
    {
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
