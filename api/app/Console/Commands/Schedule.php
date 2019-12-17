<?php

namespace App\Console\Commands;

use App\Http\Queries\MySQL\ApiQuery;
use App\Http\Utility\CustomDate;
use App\Http\Utility\Fixture;
use App\Http\Utility\PushNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

class Schedule extends Command {

    protected $name = 'schedule';

    public function calculateUserRankings() {
        $listedUsers = array();
        Log::info('**************************************************');
        Log::info('RANKING CALCULATOR STARTED');
        $users = ApiQuery::getPlayersListByTournamentPoint();
        foreach ($users as $tournamentResult) {
            $id = null;
            $ranking = null;
            $totalPoint = null;
            foreach ($tournamentResult as $user) {
                $id = $user[IDENTIFIER];
                $ranking = $user[RANKING];
                $totalPoint = $totalPoint + $user[POINT];
            }
            if ($id && $totalPoint) {
                $listedUser = array(
                    IDENTIFIER => $id,
                    RANKING => $ranking,
                    POINT => $totalPoint
                );
                array_push($listedUsers, $listedUser);
            }
        }
        usort($listedUsers, function ($a, $b) { return $b[POINT] - $a[POINT]; });
        foreach ($listedUsers as $index => $user) {
            $user[PREVIOUS_RANKING] = $user[RANKING];
            $user[RANKING] = $index + 1;
            ApiQuery::updateUserRanking($user[IDENTIFIER], $user[RANKING], $user[PREVIOUS_RANKING]);
        }
        Log::info('TOTAL RANKED USERS: ' . $users->count());
    }

    public function changeStartedTournamentsStatus() {
        $currentDate = CustomDate::getCurrentMilliseconds();
        Log::info('**************************************************');
        Log::info('TOURNAMENT STATUS CHANGER STARTED');
        $tournaments = ApiQuery::getAllOpenTournaments();
        Log::info('TOTAL OPEN TOURNAMENTS COUNT: ' . count($tournaments));
        $resultCount = 0;
        foreach ($tournaments as $tournament) {
            if ($tournament[START_DATE] - $currentDate <= 86400000) {
                $resultCount++;
                $participants = $this->getParticipants($tournament[TOURNAMENT_ID]);
                if (count($participants) < $tournament[PARTICIPANT_COUNT] - 2) {
                    ApiQuery::updateTournamentStatus($tournament, TOURNAMENT_STATUS_CANCEL);
                    Log::info('PARTICIPANT COUNT: ' . count($participants));
                    Log::info('NEEDED PARTICIPANT COUNT: ' . $tournament[PARTICIPANT_COUNT]);
                    Log::info('TOURNAMENT ' . $tournament[TOURNAMENT_ID] . ' STATUS CHANGED: FROM OPEN TO CANCEL');
                    $this->payBack($participants);
                    PushNotification::tournamentCancelled($tournament, $participants);
                } else {
                    ApiQuery::updateTournamentStatus($tournament, TOURNAMENT_STATUS_ACTIVE);
                    Log::info('TOURNAMENT ' . $tournament[TOURNAMENT_ID] . ' STATUS CHANGED: FROM OPEN TO ACTIVE');
                    Fixture::setKnockOutStartDraws($tournament[TOURNAMENT_ID], $tournament[START_DATE], $participants);
                    PushNotification::tournamentStarts($tournament, $participants);
                }
            }
        }
        Log::info('STATUS CHANGED TOURNAMENTS COUNT: ' . $resultCount);
    }

    public function payWaitingPaymentsFromTournament() {
        Log::info('**************************************************');
        Log::info('EARNINGS PAYER STARTED');
        $earnings = ApiQuery::getFinanceWithStatus(FINANCE_STATUS_WAITING);
        Log::info('TOTAL WAITING EARNINGS COUNT: ' . count($earnings));
        $totalBudget = 0;
        $totalTicket = 0;
        foreach ($earnings as $earning) {
            if ($earning[TOURNAMENT_ID]) {
                ApiQuery::updateFinanceStatus($earning[FINANCE_ID], FINANCE_STATUS_APPROVED);
                if (strtolower($earning[TYPE]) === strtolower(PLAYER)) {
                    ApiQuery::updateParticipantEarnings($earning[TOURNAMENT_ID], $earning[USER_ID], $earning[AMOUNT]);
                }
                $user = ApiQuery::getUserById($earning[USER_ID]);
                $budget = 0;
                $ticket = 0;
                if ($earning[AMOUNT] > 0) {
                    $budget = $user[BUDGET] + $earning[AMOUNT];
                    $totalBudget += $earning[AMOUNT];
                    ApiQuery::updateUserBudget($earning[USER_ID], $budget);
                } else if ($earning[TICKET] > 0) {
                    $ticket = $user[TICKET] + $earning[TICKET];
                    $totalTicket += $earning[TICKET];
                    ApiQuery::updateUserTicket($earning[USER_ID], $ticket);
                }
                Log::info('USER`S EARNINGS - ID: ' . $earning[USER_ID] . ' ' . $earning[AMOUNT] . ' ' . TURKISH_LIRA . ' ' . $earning[TICKET] . ' ' . TICKET);
                Log::info('USER`S NEW BALANCE - ID: ' . $earning[USER_ID] . ' ' . $budget . ' ' . TURKISH_LIRA . ' ' . $ticket . ' ' . TICKET);
                PushNotification::payedTournamentEarnings($earning[TOURNAMENT_ID], $earning[USER_ID], $earning[AMOUNT], $earning[TICKET]);
            }
        }
        Log::info('TOTAL PAYED EARNINGS: ' . $totalBudget . ' ' . TURKISH_LIRA . ' ' . $totalTicket . ' ' . TICKET);
    }

    private function getParticipants($tournamentId) {
        $participants = ApiQuery::getParticipants($tournamentId);
        return $participants;
    }

    private function payBack($participants) {
        foreach ($participants as $participant) {
            $user = ApiQuery::getUserById($participant[PARTICIPANT_ID]);
            if (strtolower($participant[PAYMENT_TYPE]) == strtolower(MONEY)) {
                $user[BUDGET] = number_format($participant[BUDGET] + $participant[PAYMENT_AMOUNT], 2, '.', '');
            } else if (strtolower($participant[PAYMENT_TYPE]) == strtolower(TICKET)) {
                $user[TICKET] = ($participant[TICKET] + 1);
            }
            ApiQuery::removeParticipant($participant[TOURNAMENT_ID], $participant[PARTICIPANT_ID]);
            $user->save();
            Log::info('PAYED BACK TO PARTICIPANT: ' . $participant[PARTICIPANT_ID] . ' (' . $participant[PAYMENT_TYPE] . ')');
        }
    }

}
