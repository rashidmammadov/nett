<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 18.03.2019
 * Time: 16:56
 */

namespace App\Repository\Transformers;


class TournamentTransformer extends Transformer {

    public function transform($tournament) {
        return [
            TOURNAMENT_ID => $tournament[TOURNAMENT_ID],
            CITY => $tournament[CITY],
            DISTRICT => $tournament[DISTRICT],
            HOLDER => array(
                IDENTIFIER => $tournament[IDENTIFIER],
                USERNAME => $tournament[USERNAME],
                PICTURE => $tournament[PICTURE]
            )
        ];
    }
}
