<?php

namespace App\Http\Controllers;

use App\Http\Queries\MySQL\ApiQuery;
use App\Http\Utility\TimelineReport;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class ReportController extends ApiController {

    public function __construct() {}

    public function get(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $rules = array(
                REPORT_TYPE => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                $data = array();
                if ($request[REPORT_TYPE] == TIMELINE_REPORT) {
                    $data = $this->timelineReport($user[IDENTIFIER]);
                }
                return $this->respondCreated(TIMELINE_REPORT_CREATED_SUCCESSFULLY, $data);
            }
        } catch (JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * @description Prepare participant`s tournaments result by timeline.
     * @param $userId
     * @return array
     */
    private function timelineReport($userId) {
        $queryResult = ApiQuery::getTimelineReport($userId);
        $result = array();
        foreach ($queryResult as $query) {
            $timeline = new TimelineReport($query);
            $timeline::setTournamentRanking($query[TOURNAMENT_RANKING]);
            array_push($result, $timeline::get());
        }
        return $result;
    }

}
