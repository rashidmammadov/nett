<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 02.05.2019
 * Time: 14:10
 */

namespace App\Http\Controllers;

use App\Http\Queries\MySQL\ApiQuery;
use App\Http\Utility\Finance;
use App\Http\Utility\Fixture;
use App\Http\Utility\Match;
use App\Http\Utility\Participant;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class FixtureController extends ApiController {

    public function __construct() {}

    /**
     * Handle request to set match score.
     *
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
                    if (intval($tournament[STATUS]) == TOURNAMENT_STATUS_ACTIVE) {
                        if ($request[TOURNAMENT_TYPE] == KNOCK_OUT) {
                            if ($request[HOME_POINT] != $request[AWAY_POINT]) {
                                $data = $this->setKnockOutFixtureMatchResult($request[TOURNAMENT_ID], $request[TOUR_ID],
                                    $request[MATCH_ID], $request[HOME_POINT], $request[AWAY_POINT]);
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
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
        }
    }

    /**
     * Set match result of knock out fixture.
     *
     * @param integer $tournamentId - the id of tournament.
     * @param integer $tourId - the tour id of fixture.
     * @param integer $matchId - the match id of fixture.
     * @param integer $homePoint - the point (goal count) of home player.
     * @param integer $awayPoint - the point (goal count) of away player.
     * @return array|bool - the updated result of fixture if available or returns false.
     */
    private function setKnockOutFixtureMatchResult($tournamentId, $tourId, $matchId, $homePoint, $awayPoint) {
        $matchQueryResult = ApiQuery::getMatch($tournamentId, $tourId, $matchId);
        if ($matchQueryResult[AVAILABLE]) {
            $tourName = $matchQueryResult[TOUR_NAME];
            list($winnerId, $loserId) = Match::setMatchWinner($matchId, $matchQueryResult[HOME_ID],
                $matchQueryResult[AWAY_ID], $homePoint, $awayPoint);
            Match::setNextTourMatch($tournamentId, $tourId, $tourName, $loserId, $winnerId, $matchQueryResult[DATE]);

            /** update participants` point */
            $maxPoint = max($homePoint, $awayPoint);
            $minPoint = min($homePoint, $awayPoint);
            Participant::calculatePlayerPoint($tournamentId, $winnerId, $maxPoint, ($tourId + 1) * 100);
            Participant::calculatePlayerPoint($tournamentId, $loserId, $minPoint, ($tourId + 1) * 1);
            $this->updateWinnerParticipantsRanking($tournamentId, $tourName, $winnerId, $loserId);
            $this->closeTournament($tournamentId);
            return Fixture::prepareTournamentFixtureData($tournamentId);
        } else {
            return false;
        }
    }

    /**
     * Update ranking of participant if semi final or final tour.
     *
     * @param integer $tournamentId - the id of tournament.
     * @param string $tourName - the name of tour of fixture.
     * @param integer $winnerId - the id of winner participant.
     * @param integer $loserId - the id of loser participant.
     */
    private function updateWinnerParticipantsRanking($tournamentId, $tourName, $winnerId, $loserId): void {
        if (Match::isFinal($tourName)) {
            $rankings = array(
                array(PARTICIPANT_ID => $winnerId, TOURNAMENT_RANKING => 1),
                array(PARTICIPANT_ID => $loserId, TOURNAMENT_RANKING => 2)
            );
            Participant::setKnockOutFixtureRanking($tournamentId, $rankings);
        } else if (Match::isThirdPlace($tourName)) {
            $rankings = array(
                array(PARTICIPANT_ID => $winnerId, TOURNAMENT_RANKING => 3),
                array(PARTICIPANT_ID => $loserId, TOURNAMENT_RANKING => 4)
            );
            Participant::setKnockOutFixtureRanking($tournamentId, $rankings);
        }
    }

    /**
     * Change tournament status to closed if all match has played.
     * Set participant rankings and points.
     * Calculate earnings of winners and holder.
     *
     * @param integer $tournamentId - the id of tournament.
     */
    private function closeTournament($tournamentId): void {
        if (ApiQuery::isAllMatchPlayed($tournamentId)) {
            $tournament = ApiQuery::getTournament($tournamentId);
            $rankings = Fixture::setKnockOutRanking($tournamentId);
            Participant::setKnockOutFixtureRanking($tournamentId, $rankings);
            $participants = ApiQuery::getParticipants($tournamentId);
            Finance::setKnockOutFixtureParticipantsEarnings($tournamentId, $participants, $tournament[PARTICIPATION_FEE]);
            Finance::setKnockOutFixtureHolderEarnings($tournamentId, $tournament[HOLDER_ID], count($participants),
                $tournament[PARTICIPATION_FEE]);
            ApiQuery::updateTournamentStatus($tournament, TOURNAMENT_STATUS_CLOSE);
        }
    }

}
