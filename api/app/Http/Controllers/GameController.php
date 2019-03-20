<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 20.03.2019
 * Time: 17:28
 */

namespace App\Http\Controllers;

use App\Http\Queries\MySQL\ApiQuery;
use App\Http\Utility\Game;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class GameController extends ApiController {

    public function __construct() {}

    public function add(Request $request) {
        try {
            $admin = JWTAuth::parseToken()->authenticate();
            $rules = array(
                GAME_NAME => 'required',
                PLAYING_TYPE => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                if ($admin[TYPE] == ADMIN) {
                    return $this->respondCreated(SUCCESS, $this->setGame($request));
                } else {
                    return $this->respondWithError(PERMISSION_DENIED);
                }
            }
        } catch(JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            return $this->respondWithError($e->getMessage());
        }
    }

    private function setGame($request) {
        $game = new Game($request);
        ApiQuery::setGame($game::getGame());
        return $game::getGame();
    }
}
