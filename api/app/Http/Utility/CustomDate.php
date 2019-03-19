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

    public static function getDateFromMilliseconds($date = null, $format = null) {
        if ($date) {
            if ($format == DATE) {
                $result = date(DAY_MONTH, $date / 1000);
            } else if ($format == TIME) {
                $result = date(HOUR_MINUTE, $date / 1000);
            } else {
                $result = date(DATE_FORMAT, $date / 1000);
            }
        } else {
            if ($format == DATE) {
                $result = date(DAY_MONTH);
            } else if ($format == TIME) {
                $result = date(HOUR_MINUTE);
            } else {
                $result = date(DATE_FORMAT);
            }
        }
        return $result;
    }
}
