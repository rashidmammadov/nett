<?php

namespace App\Http\Controllers;

use App\Http\Queries\MySQL\ApiQuery;
use App\Http\Utility\Finance;
use App\Http\Utility\FinanceReport;
use App\Http\Utility\TimelineReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
                if ($request[REPORT_TYPE] == FINANCE_REPORT && $user[TYPE] == PLAYER) {
                    $data = $this->financeReport($user[IDENTIFIER]);
                } else if ($request[REPORT_TYPE] == RANKING_REPORT) {
                    $data = $this->rankingReport();
                } else if ($request[REPORT_TYPE] == TIMELINE_REPORT && $user[TYPE] == PLAYER) {
                    $data = $this->timelineReport($user[IDENTIFIER]);
                }
                return $this->respondCreated(REPORT_CREATED_SUCCESSFULLY, $data);
            }
        } catch (JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * @description Prepare participant`s income and outcome finance information.
     * @param $userId
     * @return array
     */
    private function financeReport($userId) {
        $queryResult = ApiQuery::getFinanceReport($userId);
        $result = array();
        foreach ($queryResult as $group => $items) {
            $finance = new FinanceReport();
            $label = $group;
            $amount = 0;
            foreach ($items as $item) { $amount += $item[AMOUNT]; }
            $finance::setLabel($label);
            $finance::setAmount($amount);
            $finance::setCurrency(TURKISH_LIRA);
            array_push($result, $finance::get());
        }
        return $result;
    }

    /**
     * @description Prepare players ranking.
     * @return array
     */
    private function rankingReport() {
        $queryResult = ApiQuery::getRankingReport();
        return $queryResult;
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
