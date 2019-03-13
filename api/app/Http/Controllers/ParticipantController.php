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
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class ParticipantController extends ApiController {

    public function __construct() { }

    /**
     * @description: append player to given tournament.
     * @param Request $request
     * @return mixed
     */
    public function append(Request $request) {
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
                        $ifAppended = ApiQuery::checkIfAppended($request[TOURNAMENT_ID], $user[IDENTIFIER]);
                        /** check if user did not appended before **/
                        if (!$ifAppended) {
                            if (strtolower($request[PAYMENT_TYPE]) == strtolower(MONEY) && $user[BUDGET] >= MIN_AMOUNT) {
                                $data = $this->appendWithMoney($request[TOURNAMENT_ID], $user);
                            } else if (strtolower($request[PAYMENT_TYPE]) == strtolower(TICKET) && $user[TICKET] > 0) {
                                $data = $this->appendWithTicket($request[TOURNAMENT_ID], $user);
                            } else {
                                return $this->respondWithError(INVALID_PAYMENT_TYPE);
                            }
                            return $this->respondCreated(APPENDED_SUCCESSFULLY, $data);
                        } else {
                            return $this->respondWithError(ALREADY_APPENDED);
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
                    $ifAppended = ApiQuery::checkIfAppended($request[TOURNAMENT_ID], $user[IDENTIFIER]);
                    if ($ifAppended) {
                        $data = $this->leaveTournament($request[TOURNAMENT_ID], $user);
                        return $this->respondCreated(LEFT_TOURNAMENT_SUCCESSFULLY, $data);
                    } else {
                        return $this->respondWithError(DID_NOT_APPENDED);
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
     * @description: append tournament with money.
     * @param integer $tournamentId
     * @param object $user
     * @return mixed
     */
    private function appendWithMoney($tournamentId, $user) {
        $participantId = $user[IDENTIFIER];
        $params = array(
            PAYMENT_AMOUNT => MIN_AMOUNT,
            PAYMENT_TYPE => MONEY
        );
        ApiQuery::appendTournament($tournamentId, $participantId, $params);
        $user[BUDGET] = number_format($user[BUDGET] - MIN_AMOUNT, 2, '.', '');
        $user->save();

        return array(
            PAYMENT_TYPE => MONEY,
            BUDGET => $user[BUDGET]
        );
    }

    /**
     * @description: append tournament with ticket.
     * @param integer $tournamentId
     * @param object $user
     * @return mixed
     */
    private function appendWithTicket($tournamentId, $user) {
        $participantId = $user[IDENTIFIER];
        $params = array(
            PAYMENT_AMOUNT => 0,
            PAYMENT_TYPE => TICKET
        );
        ApiQuery::appendTournament($tournamentId, $participantId, $params);
        $user[TICKET] = ($user[TICKET] - 1);
        $user->save();

        return array(
            PAYMENT_TYPE => TICKET,
            TICKET => $user[TICKET]
        );
    }

    /**
     * @description: leave tournament.
     * @param integer $tournamentId
     * @param object $user
     * @return mixed
     */
    private function leaveTournament($tournamentId, $user) {
        $participant = ApiQuery::getParticipant($tournamentId, $user[IDENTIFIER]);
        ApiQuery::removeParticipant($tournamentId, $user->id);
        if (strtolower($participant[PAYMENT_TYPE]) == strtolower(MONEY)) {
            $user[BUDGET] = ($user[BUDGET] + $participant[PAYMENT_AMOUNT]);
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
