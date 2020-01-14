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

    /**
     * Prepare participant`s earning by tournament report.
     * @param integer $userId - the id of user.
     * @return array
     */
    private function earningReport($userId) {
        $limit = 10;
        $queryResult = ApiQuery::getEarningReport($userId, $limit);
        $result = array();
        $maxEarning = 0;
        foreach ($queryResult as $item) {
            $maxEarning = $item[AMOUNT] > $maxEarning ? $item[AMOUNT] : $maxEarning;
        }
        foreach ($queryResult as $item) {
            $earningReport = new EarningReport($item);
            $earningPercentage = ($item[AMOUNT] * 100) / $maxEarning;
            $earningReport->setEarnings($item[AMOUNT] . TURKISH_LIRA);
            $earningReport->setEarningPercentage($earningPercentage);
            array_push($result, $earningReport->get());
        }
        return $result;
    }

    /**
     * Prepare participant`s income and outcome finance information.
     * @param integer $userId - the id of user.
     * @return array
     */
    private function financeReport($userId) {
        $queryResult = ApiQuery::getFinanceReport($userId);
        $result = array();
        foreach ($queryResult as $group => $items) {
            $financeReport = new FinanceReport();
            $label = $group;
            $amount = 0;
            foreach ($items as $item) { $amount += $item[AMOUNT]; }
            $financeReport->setLabel($label);
            $financeReport->setAmount($amount);
            $financeReport->setCurrency(TURKISH_LIRA);
            array_push($result, $financeReport->get());
        }
        return $result;
    }

    /**
     * Prepare most played games.
     * @return array
     */
    private function mostPlayedReport() {
        $result = array();
        $mostPlayerGames = ApiQuery::getMostPlayedReport();
        foreach ($mostPlayerGames as $gameId => $statuses) {
            $mostPlayedReport = new MostPlayedReport();
            $totalCount = 0;
            foreach ($statuses as $status => $data) {
                $count = count($data);
                $mostPlayedReport->setGameId($data[0][GAME_ID]);
                $mostPlayedReport->setGameName($data[0][GAME_NAME]);
                $mostPlayedReport->setGameImage($data[0][GAME_IMAGE]);
                if ($status == TOURNAMENT_STATUS_ACTIVE) {
                    $mostPlayedReport->setActiveStatusCount($count);
                } else if ($status == TOURNAMENT_STATUS_CANCEL) {
                    $mostPlayedReport->setCancelStatusCount($count);
                } else if ($status == TOURNAMENT_STATUS_CLOSE) {
                    $mostPlayedReport->setCloseStatusCount($count);
                } else if ($status == TOURNAMENT_STATUS_OPEN) {
                    $mostPlayedReport->setOpenStatusCount($count);
                }
                $totalCount += $count;
            }
            $mostPlayedReport->setTotalCount($totalCount);
            array_push($result, $mostPlayedReport->get());
        }
        return $result;
    }

    /**
     * participant`s notifications.
     * @param integer $userId - the id of user.
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
            $notificationReport = new NotificationReport($query);
            $message = $notificationReport->prepareTournamentMessage($query[GAME_NAME], $query[START_DATE], $query[STATUS], $type);
            $notificationReport->setMessage($message);
            array_push($result, $notificationReport->get());
        }
        return $result;
    }

    /**
     * Prepare players ranking.
     * @param $user - Holds the current user data.
     * @return array - ranking report data.
     */
    private function rankingReport($user) {
        $limit = 10;
        $queryResult = ApiQuery::getRankingReport($limit);
        $result = array();
        $userOnTopList = false;
        foreach ($queryResult as $player) {
            $rankingReport = new RankingReport($player);
            if ($player[IDENTIFIER] == $user[IDENTIFIER]) {
                $userOnTopList = true;
            }
            $rankingReport->setPreviousRanking($player[PREVIOUS_RANKING]);
            array_push($result, $rankingReport->get());
        }
        if (!$userOnTopList) {
            $rankingReport = new RankingReport($user);
            $rankingReport->setRanking($user[RANKING]);
            $rankingReport->setPreviousRanking($user[PREVIOUS_RANKING]);
            array_push($result, $rankingReport->get());
        }
        return $result;
    }

    /**
     * Prepare participant`s tournaments result by timeline.
     * @param integer $userId - the id of user.
     * @return array - timeline report data.
     */
    private function timelineReport($userId) {
        $queryResult = ApiQuery::getTimelineReport($userId);
        $result = array();
        foreach ($queryResult as $query) {
            $timelineReport = new TimelineReport($query);
            $timelineReport->setTournamentRanking($query[TOURNAMENT_RANKING]);
            array_push($result, $timelineReport->get());
        }
        return $result;
    }

}
