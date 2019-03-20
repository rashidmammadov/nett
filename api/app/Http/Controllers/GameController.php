<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 20.03.2019
 * Time: 17:28
 */

namespace App\Http\Controllers;

use App\Http\Queries\MySQL\ApiQuery;
use App\Repository\Transformers\GameTransformer;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class GameController extends ApiController {

    private $gameTransformer;

    public function __construct(GameTransformer $gameTransformer) {
        $this->gameTransformer = $gameTransformer;
    }

    /**
     * @description: handle request to create new game
     * @param Request $request
     * @return mixed: game info
     */
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
                    $result = $this->setGame($request);
                    return $this->respondCreated(SUCCESS, $result);
                } else {
                    return $this->respondWithError(PERMISSION_DENIED);
                }
            }
        } catch(JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * @description: handle request to get games list
     * @param integer $gameId
     * @return mixed: game info
     */
    public function get($gameId = null) {
        try {
            JWTAuth::parseToken()->authenticate();
            $result = $this->getGames($gameId);
            return $this->respondCreated(SUCCESS, $result);
        } catch(JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * @description: send request to add new game
     * @param Request $request
     * @return mixed: query result
     */
    private function setGame($request) {
        return ApiQuery::setGame($request);
    }

    /**
     * @description: send request to get games
     * @param integer $gameId
     * @return mixed: query result
     */
    private function getGames($gameId = null) {
        $games = ApiQuery::getGame($gameId);
        $gameList = array();
        foreach ($games as $game) {
            array_push($gameList, $this->gameTransformer->transform($game));
        }
        return $gameId ? $gameList[0] : $gameList;
    }
}
