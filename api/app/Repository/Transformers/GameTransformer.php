<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 20.03.2019
 * Time: 23:15
 */

namespace App\Repository\Transformers;


class GameTransformer extends Transformer {

    public function transform($game) {
        return [
            GAME_ID => $game[GAME_ID],
            GAME_NAME => $game[GAME_NAME],
            GAME_IMAGE => $game[GAME_IMAGE],
            PLAYING_TYPE => json_decode($game[PLAYING_TYPE])
        ];
    }
}
