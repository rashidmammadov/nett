<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 06.03.2019
 * Time: 15:57
 */

namespace App\Http\Utility;

use Illuminate\Support\Facades\Storage;

class Fixture {

    const MAX_PARTICIPANT = 8;

    public static function set($parameters) {
        $fixture = array(
            TOURNAMENT_ID => 1,
            HOLDER => $parameters[HOLDER],
            GAME_ID => $parameters[GAME_ID],
            TOURNAMENT_TYPE => $parameters[TOURNAMENT_TYPE],
            PARTICIPANT_COUNT => $parameters[PARTICIPANT_COUNT],
            CREATED_AT => date(DATE_FORMAT),
            DRAWS => array()
        );

        $startDate = $parameters[START_DATE] / 1000;

        $matchCount = self::MAX_PARTICIPANT / 2;
        while ($matchCount != 1) {
            $draw = self::setDefaultDraw($matchCount, (1 . '/' . $matchCount), $startDate);
            array_push($fixture[DRAWS], $draw);
            $matchCount = $matchCount / 2;
            if ($matchCount == 1) {
                $thirdPlace = self::setDefaultDraw(1, '3th', $startDate);
                array_push($fixture[DRAWS], $thirdPlace);
                $final = self::setDefaultDraw(1, 'final', $startDate);
                array_push($fixture[DRAWS], $final);
            }
        }

        Storage::disk('local')->put('fixtures/' . $fixture[TOURNAMENT_ID] . '.json', json_encode($fixture));

        return $fixture;
    }

    private static function setDefaultDraw($count, $title, $date) {
        $draw = array(
            DRAW_TITLE => $title,
            MATCHES => self::setDefaultMatches($count, date(DATE_FORMAT, $date))
        );
        return $draw;
    }

    private static function setDefaultMatches($count, $date) {
        $matches = array();
        for ($i = 0; $i < $count; $i++) {
            $match = new Match(array(UPDATED_AT => date(DATE_FORMAT)));
            $match::setDate($date);
            array_push($matches, $match::getMatch());
        }
        return $matches;
    }
}
