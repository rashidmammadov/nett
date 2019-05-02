<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 02.05.2019
 * Time: 14:10
 */

namespace App\Http\Controllers;

use App\Http\Queries\MySQL\ApiQuery;
use App\Http\Utility\CustomDate;
use App\Http\Utility\Fixture;
use App\Http\Utility\Match;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class FixtureController extends ApiController {

    public function __construct() {}

    public function setScore(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $rules = array(
                TOURNAMENT_ID => 'required',
                TOURNAMENT_TYPE => 'required',
                TOUR_ID => 'required',
                MATCH_ID => 'required',
                HOME_POINT => 'required',
                AWAY_POINT => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                $tournament = ApiQuery::getTournament($request[TOURNAMENT_ID]);
                if ($user[TYPE] == HOLDER && $tournament[HOLDER_ID] == $user[IDENTIFIER]) {
                    if ($tournament[STATUS] === TOURNAMENT_STATUS_ACTIVE) {
                        if ($request[TOURNAMENT_TYPE] == KNOCK_OUT) {
                            $this->setKnockOutFixtureResult($request);
                        }
                    } else {
                        return $this->respondWithError(STATUS_MUST_BE_ACTIVE_FOR_CHANGE);
                    }
                } else {
                    return $this->respondWithError(PERMISSION_DENIED);
                }
            }
        } catch (JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            return $this->respondWithError($e->getMessage());
        }
    }

    private function setKnockOutFixtureResult($request) {
        $queryResult = ApiQuery::getFixture($request[TOURNAMENT_ID]);
        $jsonData = json_decode($queryResult[FIXTURE], true);
        $fixture = new Fixture($jsonData);
        $draws = $fixture::getDraws();

        foreach ($draws as $draw) {
            foreach ($draw[MATCHES] as $match) {
                if ($match[TOUR_ID] == $request[TOUR_ID] && $match[MATCH_ID] == $request[MATCH_ID]) {
                    $matchObject = new Match($match);
                    $home = $matchObject::getHome();
                    $away = $matchObject::getAway();

                    $home[POINT] = $request[HOME_POINT];
                    $away[POINT] = $request[AWAY_POINT];

                    if ($request[HOME_POINT] > $request[AWAY_POINT]) {
                        $matchObject::setWinner($home);
                        $matchObject::setLoser($away);
                    } else if ($request[HOME_POINT] < $request[AWAY_POINT]) {
                        $matchObject::setWinner($away);
                        $matchObject::setLoser($home);
                    }

                    $matchObject::setHome($home);
                    $matchObject::setAway($away);
                    $matchObject::setUpdatedAt(CustomDate::getDateFromMilliseconds());
                    Log::info(json_encode($matchObject::getMatch()));
                }
            }
        }
    }
}
