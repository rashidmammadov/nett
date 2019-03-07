<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 06.03.2019
 * Time: 15:57
 */

namespace App\Http\Controllers;

use App\Http\Utility\Fixture;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class TournamentController extends ApiController {

    public function __construct() { }

    public function add(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $rules = array(
                GAME_ID => 'required',
                TOURNAMENT_TYPE => 'required',
                PARTICIPANT_COUNT => 'required',
                START_DATE => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                if ($user->type == HOLDER) {
                    $fixture = Fixture::set($request);
                    return $this->respondCreated(SUCCESS, $fixture);
                } else {
                    return $this->respondWithError(PERMISSION_DENIED);
                }
            }
        } catch(JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            return $this->respondWithError($e->getMessage());
        }
    }
}
