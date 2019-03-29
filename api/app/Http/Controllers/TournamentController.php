<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 06.03.2019
 * Time: 15:57
 */

namespace App\Http\Controllers;

use App\Http\Queries\MySQL\ApiQuery;
use App\Http\Utility\CustomDate;
use App\Http\Utility\Fixture;
use App\Repository\Transformers\TournamentTransformer;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class TournamentController extends ApiController {

    private $tournamentTransformer;

    public function __construct(TournamentTransformer $tournamentTransformer) {
        $this->tournamentTransformer = $tournamentTransformer;
    }

    /**
     * @description: handle request to create new tournament
     * @param Request $request
     * @return mixed: tournament info
     */
    public function add(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $rules = array(
                GAME_ID => 'required',
                TOURNAMENT_TYPE => 'required',
                PARTICIPANT_COUNT => 'required|numeric|min:16|max:32',
                START_DATE => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                if ($user->type == HOLDER) {
                    $request[START_DATE] = (string)$request[START_DATE];
                    if ($request[START_DATE] > CustomDate::getCurrentMilliseconds()) {
                        if (ApiQuery::checkTournamentsDifferenceIsOneDay($request, $user[IDENTIFIER])) {
                            $request[STATUS] = TOURNAMENT_STATUS_OPEN;
                            $tournament = $this->setTournament($user->id, $request);
                            $this->setDefaultFixture($tournament);
                            return $this->respondCreated(TOURNAMENT_CREATED_SUCCESSFULLY, $tournament);
                        } else {
                            return $this->respondWithError(ALREADY_HAVE_TOURNAMENT_ON_THIS_DATE);
                        }
                    } else {
                        return $this->respondWithError(INVALID_DATE);
                    }
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
     * @description: handle request to get tournaments
     * @param Request $request
     * @return mixed: tournament info
     */
    public function get(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $rules = array(
                STATUS => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                $tournaments = $this->getTournaments($request, $user);
                return $this->respondCreated(SUCCESS, $tournaments);
            }
        } catch(JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * @description: handle request to get user`s tournaments
     * @param Request $request
     * @return mixed: tournament info
     */
    public function getMine(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $rules = array(
                STATUS => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                $tournaments = $this->getMyTournaments($request, $user);
                return $this->respondCreated(SUCCESS, $tournaments);
            }
        } catch(JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * @description: get tournaments which is matched with given parameters
     * @param Request $request
     * @param array $user
     * @return mixed: tournament info
     */
    private function getTournaments($request, $user) {
        $tournaments = ApiQuery::getTournaments($request, $user);
        $tournamentsList = array();
        foreach ($tournaments as $tournament) {
            $data = $this->prepareTournamentGeneralData($tournament, $user);
            array_push($tournamentsList, $data);
        }
        return $tournamentsList;
    }

    /**
     * @description: get user`s tournaments which is matched with given parameters
     * @param Request $request
     * @param array $user
     * @return mixed: tournament info
     */
    private function getMyTournaments($request, $user) {
        $result = array();
        $tournaments = array();
        if ($user[TYPE] === PLAYER) {
            $tournaments = ApiQuery::getParticipantTournaments($request, $user);
        }
        foreach ($tournaments as $tournament) {
            if ($tournament[TOURNAMENT_ID]) {
                $data = $this->prepareTournamentGeneralData($tournament, $user, true);
                array_push($result, $data);
            }
        }
        return $result;
    }

    /**
     * @description: put tournament data to database
     * @param Integer $userId
     * @param Request $request
     * @return mixed: tournament info
     */
    private function setTournament($userId, $request) {
        $request[HOLDER_ID] = $userId;
        return ApiQuery::setTournament($request);
    }

    /**
     * @description: prepare tournament`s fixture with given type.
     * @param array $parameters
     */
    private function setDefaultFixture($parameters) {
        $fixture = new Fixture($parameters);
        $fixture::setCreatedAt(CustomDate::getDateFromMilliseconds());
        $draws = array();
        if ($parameters[TOURNAMENT_TYPE] == KNOCK_OUT) {
            $draws = $fixture::setKnockOutDraws($parameters[START_DATE], $parameters[DAYS]);
        }
        $fixture::setDraws($draws);
        ApiQuery::setFixture($parameters[TOURNAMENT_ID], $fixture::getFixture());
    }

    /**
     * @description: prepare tournament`s general data to show timeline post.
     * @param $tournament
     * @param $user
     * @param boolean $attended - if get participant`s tournament don`t check twice.
     * @return array
     */
    private function prepareTournamentGeneralData($tournament, $user, $attended = false) {
        $tournament[CURRENT_PARTICIPANTS] = ApiQuery::getParticipants($tournament[TOURNAMENT_ID])->count();
        if ($attended) {
            $tournament[ATTENDED] = true;
        } else {
            $tournament[ATTENDED] = ApiQuery::checkIfAttended($tournament[TOURNAMENT_ID], $user[IDENTIFIER]);
        }

        if ($tournament[ATTENDED]) {
            $participatedUser = ApiQuery::getParticipants($tournament[TOURNAMENT_ID], $user[IDENTIFIER])->first();
            $tournament[REFERENCE_CODE] = $participatedUser[REFERENCE_CODE];
        }
        return $this->tournamentTransformer->transform($tournament);
    }

}
