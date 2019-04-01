<?php

namespace App\Console\Commands;

use App\Http\Queries\MySQL\ApiQuery;
use App\Http\Utility\CustomDate;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

class Schedule extends Command {

    protected $name = 'schedule';

    public function changeStartedTournamentsStatus() {
        $currentDate = CustomDate::getCurrentMilliseconds();
        Log::info('started tournament status changer');
        $tournaments = ApiQuery::getAllOpenTournaments();
        Log::info('total open tournaments count: ' . count($tournaments));
        $resultCount = 0;
        foreach ($tournaments as $tournament) {
            // TODO: check min participant count / set fixture / send notification to holder and participant
            if ($tournament[START_DATE] - $currentDate <= 86400000) {
                $resultCount++;
                ApiQuery::updateTournamentStatus($tournament, TOURNAMENT_STATUS_ACTIVE);
                Log::info('tournament ' . $tournament[TOURNAMENT_ID] . ' : status changed to active from open');
            }
        }
        Log::info('status changed tournaments count: ' . $resultCount);
    }
}
