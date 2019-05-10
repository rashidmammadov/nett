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
use App\Http\Utility\Finance;
use App\Http\Utility\Fixture;
use App\Http\Utility\Match;
use App\Http\Utility\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class FixtureController extends ApiController {

    public function __construct() {}

    /**
     * @description: handle request to set match score
     * @param Request $request
     * @return mixed: tournament info
     */
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
                            if ($request[HOME_POINT] != $request[AWAY_POINT]) {
                                $data = $this->setKnockOutFixtureResult($request);
                                if ($data) {
                                    return $this->respondCreated(MATCH_RESULT_UPDATED_SUCCESSFULLY, $data);
                                } else {
                                    return $this->respondWithError(RESULT_ALREADY_SET);
                                }
                            } else {
                                return $this->respondWithError(MUST_BE_ONE_WINNER);
                            }
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

    /**
     * @description: update knock out tournament fixture`s draw
     * @param Request $request
     * @return mixed: updated knock out tournament info
     */
    private function setKnockOutFixtureResult($request) {
        $tourId = $request[TOUR_ID];
        $matchId = $request[MATCH_ID];
        $queryResult = ApiQuery::getFixture($request[TOURNAMENT_ID]);
        $jsonData = json_decode($queryResult[FIXTURE], true);
        $fixture = new Fixture($jsonData);
        $draws = $fixture::getDraws();

        /** find winner of given match */
        $match = $draws[$tourId][MATCHES][$matchId];
        if ($match[AVAILABLE]) {
            $isFinal = false;
            $updatedMatch = Match::setMatchWinner($match, $request[HOME_POINT], $request[AWAY_POINT]);
            $draws[$tourId][MATCHES][$matchId] = $updatedMatch;
            if ($tourId == count($draws) - 1 || $draws[$tourId][DRAW_TITLE] == 'final') {
                $isFinal = true;
            }

            $winner = $updatedMatch[WINNER];
            $loser = $updatedMatch[LOSER];
            if (count($draws[$tourId][MATCHES]) == 2) {
                /** set 3th and final matches players if round is semi-final */
                $thirdPlaceDraw = $draws[$tourId + 1];
                $finalDraw = $draws[$tourId + 2];
                $draws[$tourId + 1] = Fixture::setKnockOutNextDraw($thirdPlaceDraw, $loser);
                $draws[$tourId + 2] = Fixture::setKnockOutNextDraw($finalDraw, $winner);
            } else if (count($draws[$tourId][MATCHES]) > 2) {
                /** append winner player to the next round */
                $nextDraw = $draws[$tourId + 1];
                $draws[$tourId + 1] = Fixture::setKnockOutNextDraw($nextDraw, $winner);
            }

            /** update player`s point */
            $maxPoint = max($request[HOME_POINT], $request[AWAY_POINT]);
            $minPoint = min($request[HOME_POINT], $request[AWAY_POINT]);
            Participant::calculatePlayerPoint($request[TOURNAMENT_ID], $winner[PARTICIPANT_ID], $maxPoint, true);
            Participant::calculatePlayerPoint($request[TOURNAMENT_ID], $loser[PARTICIPANT_ID], $minPoint, false);

            if ($isFinal) {
                $rankings = Fixture::setKnockOutRanking($jsonData);
                Participant::setKnockOutFixtureRanking($request[TOURNAMENT_ID], $rankings);
                Finance::setKnockOutFixtureParticipantsEarnings($request[TOURNAMENT_ID], $rankings);
                Finance::setKnockOutFixtureHolderEarnings($request[TOURNAMENT_ID], $fixture::getHolderId(), count($rankings));
                $tournament = ApiQuery::getTournament($request[TOURNAMENT_ID]);
                ApiQuery::updateTournamentStatus($tournament, TOURNAMENT_STATUS_CLOSE);
            }

            /** set updated draws */
            $fixture::setDraws($draws);
            ApiQuery::updateFixture($request[TOURNAMENT_ID], $fixture::getFixture());

            return $fixture::getFixture();
        } else {
            return false;
        }
    }
}
