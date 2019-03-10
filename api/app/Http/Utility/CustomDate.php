<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 10.03.2019
 * Time: 13:59
 */

namespace App\Http\Utility;


class CustomDate {

    public static function getCurrentMilliseconds() {
        return round(microtime(true) * 1000);
    }

    public static function getDateFromMilliseconds($date = null) {
        $result = date(DATE_FORMAT);
        if ($date) {
            $result = date(DATE_FORMAT, $date / 1000);
        }
        return $result;
    }
}
