<?php

namespace App\Http\Utility;

class NotificationReport {

    private static $tournamentId;
    private static $status;
    private static $message;

    public function __construct($parameters = null) {
        !empty($parameters[TOURNAMENT_ID])      && self::setTournamentId($parameters[TOURNAMENT_ID]);
        self::setStatus($parameters[STATUS]);
        !empty($parameters[MESSAGE])            && self::setMessage($parameters[MESSAGE]);
    }

    public static function get() {
        return array(
            TOURNAMENT_ID => self::getTournamentId(),
            STATUS => self::getStatus(),
            MESSAGE => self::getMessage()
        );
    }

    public static function getTournamentId() { return self::$tournamentId; }

    public static function setTournamentId($tournamentId): void { self::$tournamentId = $tournamentId; }

    public static function getStatus() { return self::$status; }

    public static function setStatus($status = 0): void { self::$status = $status; }

    public static function getMessage() { return self::$message; }

    public static function setMessage($message): void { self::$message = $message; }

    public static function prepareTournamentMessage($gameName, $startDate, $status) {
        $date = CustomDate::getDateFromMilliseconds($startDate);
        $message = '';
        if ($status === TOURNAMENT_STATUS_OPEN) {
            $message = '<b>' . $date . '</b> tarihli <b>' . $gameName . '</b> turnuvasına kayıtlandın.';
        } else if ($status === TOURNAMENT_STATUS_ACTIVE) {
            $message = '<b>' . $date . '</b> tarihli <b>' . $gameName . '</b> turnuvası başladı, rakiplerini ve fikstürü takip et.';
        } else if ($status === TOURNAMENT_STATUS_CLOSE) {
            $message = '<b>' . $date . '</b> tarihli <b>' . $gameName . '</b> turnuvası sonuçlandı.';
        }
        return $message;
    }

}
