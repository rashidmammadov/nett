<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 18.03.2019
 * Time: 16:56
 */

namespace App\Repository\Transformers;

use App\Http\Utility\CustomDate;
use App\Http\Utility\Fixture;

class TournamentTransformer extends Transformer {

    public function transform($tournament) {
        return [
            TOURNAMENT_ID => $tournament[TOURNAMENT_ID],
            ATTENDED => $tournament[ATTENDED],
            DATE => CustomDate::getDateFromMilliseconds($tournament[START_DATE], DATE),
            TIME => CustomDate::getDateFromMilliseconds($tournament[START_DATE], TIME),
            PARTICIPANT_COUNT => $tournament[PARTICIPANT_COUNT],
            CURRENT_PARTICIPANTS => $tournament[CURRENT_PARTICIPANTS],
            STATUS => $tournament[STATUS],
            REFERENCE_CODE => $tournament[REFERENCE_CODE],
            TOURNAMENT_TYPE => $tournament[TOURNAMENT_TYPE],
            PRICE => array(
                AMOUNT => MIN_AMOUNT,
                CURRENCY => TURKISH_LIRA,
            ),
            GAME => array(
                GAME_ID => $tournament[GAME_ID],
                GAME_NAME => $tournament[GAME_NAME],
                GAME_IMAGE => $tournament[GAME_IMAGE]
            ),
            HOLDER => array(
                IDENTIFIER => $tournament[IDENTIFIER],
                USERNAME => $tournament[USERNAME],
                PICTURE => $tournament[PICTURE],
                CITY => $tournament[CITY],
                DISTRICT => $tournament[DISTRICT],
                ADDRESS => $tournament[ADDRESS]
            ),
            PARTICIPANTS => $tournament[PARTICIPANTS],
            FIXTURE => $tournament[FIXTURE]
        ];
    }
}
