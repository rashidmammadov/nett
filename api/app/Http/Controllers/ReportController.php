<?php

namespace App\Http\Controllers;

use App\Http\Queries\MySQL\ApiQuery;
use App\Http\Utility\EarningReport;
use App\Http\Utility\FinanceReport;
use App\Http\Utility\MostPlayedReport;
use App\Http\Utility\NotificationReport;
use App\Http\Utility\RankingReport;
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
                if ($request[REPORT_TYPE] == EARNING_REPORT && $user[TYPE] == HOLDER) {
                    $data = $this->earningReport($user[IDENTIFIER]);
                } else if ($request[REPORT_TYPE] == FINANCE_REPORT) {
                    $data = $this->financeReport($user[IDENTIFIER]);
                } else if ($request[REPORT_TYPE] == MOST_PLAYED_REPORT) {
                    $data = $this->mostPlayedReport();
                } else if ($request[REPORT_TYPE] == NOTIFICATION_REPORT) {
                    $data = $this->notificationReport($user[IDENTIFIER], $user[TYPE]);
                } else if ($request[REPORT_TYPE] == RANKING_REPORT) {
                    $data = $this->rankingReport($user);
                } else if ($request[REPORT_TYPE] == TIMELINE_REPORT && $user[TYPE] == PLAYER) {
                    $data = $this->timelineReport($user[IDENTIFIER]);
                }
                return $this->respondCreated(REPORT_CREATED_SUCCESSFULLY, $data);
            }
        } catch (JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
        }
    }

    private function earningReport($userId) {
        $queryResult = ApiQuery::getEarningReport($userId);
        $result = array();
        $maxEarning = 0;
        foreach ($queryResult as $item) {
            $maxEarning = $item[AMOUNT] > $maxEarning ? $item[AMOUNT] : $maxEarning;
        }
        foreach ($queryResult as $item) {
            $report = new EarningReport($item);
            $earningPercentage = ($item[AMOUNT] * 100) / $maxEarning;
            $report::setEarnings($item[AMOUNT] . TURKISH_LIRA);
            $report::setEarningPercentage($earningPercentage);
            array_push($result, $report::get());
        }
        return $result;
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
     * @description Prepare most played games.
     * @return array
     */
    private function mostPlayedReport() {
        $result = array();
        $mostPlayerGames = ApiQuery::getMostPlayedReport();
        foreach ($mostPlayerGames as $gameId => $statuses) {
            $mostPlayed = new MostPlayedReport();
            $totalCount = 0;
            foreach ($statuses as $status => $data) {
                $count = count($data);
                $mostPlayed::setGameId($data[0][GAME_ID]);
                $mostPlayed::setGameName($data[0][GAME_NAME]);
                $mostPlayed::setGameImage($data[0][GAME_IMAGE]);
                if ($status == TOURNAMENT_STATUS_ACTIVE) {
                    $mostPlayed::setActiveStatusCount($count);
                } else if ($status == TOURNAMENT_STATUS_CANCEL) {
                    $mostPlayed::setCancelStatusCount($count);
                } else if ($status == TOURNAMENT_STATUS_CLOSE) {
                    $mostPlayed::setCloseStatusCount($count);
                } else if ($status == TOURNAMENT_STATUS_OPEN) {
                    $mostPlayed::setOpenStatusCount($count);
                }
                $totalCount += $count;
            }
            $mostPlayed::setTotalCount($totalCount);
            array_push($result, $mostPlayed::get());
        }
        return $result;
    }

    /**
     * @description Prepare participant`s notifications.
     * @param integer $userId
     * @param string $type
     * @return array
     */
    private function notificationReport($userId, $type) {
        $limit = 10;
        $result = array();
        $queryResult = array();
        if ($type == HOLDER) {
            $queryResult = ApiQuery::getHolderNotificationReport($userId, $limit);
        } else if ($type == PLAYER) {
            $queryResult = ApiQuery::getPlayerNotificationReport($userId, $limit);
        }
        foreach ($queryResult as $query) {
            $notification = new NotificationReport($query);
            $message = NotificationReport::prepareTournamentMessage($query[GAME_NAME], $query[START_DATE], $query[STATUS], $type);
            $notification::setMessage($message);
            array_push($result, $notification::get());
        }
        return $result;
    }

    /**
     * @description Prepare players ranking.
     * @param $user - Holds the current user data.
     * @return array
     */
    private function rankingReport($user) {
        $limit = 10;
        $queryResult = ApiQuery::getRankingReport($limit);
        $result = array();
        $userOnTopList = false;
        foreach ($queryResult as $player) {
            $ranking = new RankingReport($player);
            if ($player[IDENTIFIER] == $user[IDENTIFIER]) {
                $userOnTopList = true;
            }
            $ranking::setPreviousRanking($player[PREVIOUS_RANKING]);
            array_push($result, $ranking::get());
        }
        if (!$userOnTopList) {
            $ranking = new RankingReport($user);
            $ranking::setRanking($user[RANKING]);
            $ranking::setPreviousRanking($user[PREVIOUS_RANKING]);
            array_push($result, $ranking::get());
        }
        return $result;
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
