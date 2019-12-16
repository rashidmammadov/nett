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
use App\Http\Utility\Match;
use App\Repository\Transformers\ParticipantTransformer;
use App\Repository\Transformers\TournamentTransformer;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class TournamentController extends ApiController {

    private $participantTransformer;
    private $tournamentTransformer;

    public function __construct(ParticipantTransformer $participantTransformer, TournamentTransformer $tournamentTransformer) {
        $this->participantTransformer = $participantTransformer;
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
                PARTICIPATION_FEE => 'required|numeric|min:15|max:20',
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
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
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
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
        }
    }

    /**
     * @description: handle request to get tournaments
     * @param integer $tournamentId
     * @return mixed: tournament info
     */
    public function getDetail($tournamentId) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $tournament = ApiQuery::getTournamentWithDetail($tournamentId);
            $tournament[PARTICIPANTS] = $this->prepareTournamentParticipantsData($tournamentId, $user);
            $tournament[FIXTURE] = Fixture::prepareTournamentFixtureData($tournamentId);
            $result = $this->prepareTournamentGeneralData($tournament, $user);
            return $this->respondCreated(SUCCESS, $result);
        } catch (JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
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
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
        }
    }

    /**
     * @description: get tournaments which is matched with given parameters
     * @param Request $request
     * @param array $user
     * @return mixed: tournament info
     */
    private function getTournaments($request, $user) {
        $tournaments = ApiQuery::searchTournaments($request, $user);
        $tournamentsList = array();
        foreach ($tournaments as $tournament) {
            $tournament[FIXTURE] = null;
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
        if (strtolower($user[TYPE]) == strtolower(PLAYER)) {
            $tournaments = ApiQuery::searchParticipantTournaments($request, $user);
        } else if (strtolower($user[TYPE]) == strtolower(HOLDER)) {
            $tournaments = ApiQuery::searchHolderTournaments($request, $user);
        }
        foreach ($tournaments as $tournament) {
            if ($tournament[TOURNAMENT_ID]) {
                $tournament[FIXTURE] = null;
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

    /**
     * @description: prepare tournament`s participants data.
     * @param integer $tournamentId
     * @param $user
     * @return mixed
     */
    private function prepareTournamentParticipantsData($tournamentId, $user) {
        $transformedParticipants = array();
        $participants = ApiQuery::getParticipants($tournamentId);
        foreach ($participants as $participant) {
            if ($user[TYPE] == HOLDER || $user[TYPE] == ADMIN) {
                array_push($transformedParticipants, $this->participantTransformer->transformForHolder($participant));
            } else {
                array_push($transformedParticipants, $this->participantTransformer->transform($participant));
            }
        }
        return $transformedParticipants;
    }

}
