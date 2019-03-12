<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 12.03.2019
 * Time: 17:26
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class ParticipantController extends ApiController {

    public function __construct() { }

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
                if ($user->type == GAMER) {
                    if ($request[PAYMENT_TYPE] == MONEY && $request[PAYMENT_AMOUNT] > 0) {
                        $result =  $this->appendWithMoney($request);
                    } else if ($request[PAYMENT_TYPE] == TICKET) {
                        $result = $this->appendWithTicket($request);
                    } else {
                        return $this->respondWithError(INVALID_PAYMENT_TYPE);
                    }

                    return $this->respondCreated(APPENDED_SUCCESSFULLY, $result);
                } else {
                    return $this->respondWithError(PERMISSION_DENIED);
                }
            }
        } catch (JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            return $this->respondWithError($e->getMessage());
        }
    }

    private function appendWithMoney($request) {
        return $request;
    }

    private function appendWithTicket($request) {

    }
}
