<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 12.03.2019
 * Time: 17:26
 */

namespace App\Http\Controllers;

use App\Http\Queries\MySQL\ApiQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class ParticipantController extends ApiController {

    public function __construct() { }

    /**
     * @description: attend player to given tournament.
     * @param Request $request
     * @return mixed
     */
    public function attend(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $rules = array(
                TOURNAMENT_ID => 'required',
                PAYMENT_TYPE => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                /** check if user type is player **/
                if ($user->type == PLAYER) {
                    $tournament = ApiQuery::getTournament($request[TOURNAMENT_ID]);
                    /** check if tournament registration is active **/
                    if ($tournament[STATUS] == TOURNAMENT_STATUS_OPEN) {
                        $participantCount = ApiQuery::getParticipants($request[TOURNAMENT_ID])->count();
                        /** check if participant count is available **/
                        if ($participantCount < $tournament[PARTICIPANT_COUNT]) {
                            $ifAttended = ApiQuery::checkIfAttended($request[TOURNAMENT_ID], $user[IDENTIFIER]);
                            /** check if user did not attended before **/
                            if (!$ifAttended) {
                                if (strtolower($request[PAYMENT_TYPE]) == strtolower(MONEY) && $user[BUDGET] >= MIN_AMOUNT) {
                                    $data = $this->attendWithMoney($request[TOURNAMENT_ID], $user);
                                } else if (strtolower($request[PAYMENT_TYPE]) == strtolower(TICKET) && $user[TICKET] > 0) {
                                    $data = $this->attendWithTicket($request[TOURNAMENT_ID], $user);
                                } else {
                                    return $this->respondWithError(INVALID_PAYMENT_TYPE);
                                }
                                $data[CURRENT_PARTICIPANTS] = $participantCount + 1;
                                return $this->respondCreated(ATTENDED_SUCCESSFULLY, $data);
                            } else {
                                return $this->respondWithError(ALREADY_ATTENDED);
                            }
                        } else {
                            return $this->respondWithError(MAXIMUM_PARTICIPANTS_COUNT_LIMIT);
                        }
                    } else {
                        return $this->respondWithError(EXPIRED_TOURNAMENT);
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
     * @description: leave participant from tournament.
     * @param Request $request
     * @return mixed
     */
    public function leave(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $rules = array(
                TOURNAMENT_ID => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                $tournament = ApiQuery::getTournament($request[TOURNAMENT_ID]);
                if ($tournament->status == TOURNAMENT_STATUS_OPEN) {
                    $ifAttended = ApiQuery::checkIfAttended($request[TOURNAMENT_ID], $user[IDENTIFIER]);
                    if ($ifAttended) {
                        $data = $this->leaveTournament($request[TOURNAMENT_ID], $user);
                        $data[CURRENT_PARTICIPANTS] = ApiQuery::getParticipants($request[TOURNAMENT_ID])->count();
                        return $this->respondCreated(LEFT_TOURNAMENT_SUCCESSFULLY, $data);
                    } else {
                        return $this->respondWithError(DID_NOT_ATTENDED);
                    }
                } else {
                    return $this->respondWithError(EXPIRED_TOURNAMENT);
                }
            }
        } catch (JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * @description: attend tournament with money.
     * @param integer $tournamentId
     * @param object $user
     * @return mixed
     */
    private function attendWithMoney($tournamentId, $user) {
        $participantId = $user[IDENTIFIER];
        $referenceCode = Str::random(8);
        $params = array(
            PAYMENT_AMOUNT => MIN_AMOUNT,
            PAYMENT_TYPE => MONEY,
            REFERENCE_CODE => $referenceCode
        );
        ApiQuery::attendTournament($tournamentId, $participantId, $params);
        $user[BUDGET] = number_format($user[BUDGET] - MIN_AMOUNT, 2, '.', '');
        $user->save();

        return array(
            PAYMENT_TYPE => MONEY,
            BUDGET => $user[BUDGET],
            TICKET => $user[TICKET],
            REFERENCE_CODE => $referenceCode
        );
    }

    /**
     * @description: attend tournament with ticket.
     * @param integer $tournamentId
     * @param object $user
     * @return mixed
     */
    private function attendWithTicket($tournamentId, $user) {
        $participantId = $user[IDENTIFIER];
        $referenceCode = Str::random(8);
        $params = array(
            PAYMENT_AMOUNT => 0,
            PAYMENT_TYPE => TICKET,
            REFERENCE_CODE => $referenceCode
        );
        ApiQuery::attendTournament($tournamentId, $participantId, $params);
        $user[TICKET] = ($user[TICKET] - 1);
        $user->save();

        return array(
            PAYMENT_TYPE => TICKET,
            BUDGET => $user[BUDGET],
            TICKET => $user[TICKET],
            REFERENCE_CODE => $referenceCode
        );
    }

    /**
     * @description: leave tournament.
     * @param integer $tournamentId
     * @param object $user
     * @return mixed
     */
    private function leaveTournament($tournamentId, $user) {
        $participant = ApiQuery::getParticipants($tournamentId, $user[IDENTIFIER])->first();
        ApiQuery::removeParticipant($tournamentId, $user->id);
        if (strtolower($participant[PAYMENT_TYPE]) == strtolower(MONEY)) {
            $user[BUDGET] = number_format($user[BUDGET] + $participant[PAYMENT_AMOUNT], 2, '.', '');
        } else if (strtolower($participant[PAYMENT_TYPE]) == strtolower(TICKET)) {
            $user[TICKET] = ($user[TICKET] + 1);
        }
        $user->save();

        return array(
            PAYMENT_TYPE => $participant[PAYMENT_TYPE],
            BUDGET => $user[BUDGET],
            TICKET => $user[TICKET]
        );
    }
}
