<?php

namespace App\Http\Utility;

class NotificationReport {

    private static $tournamentId;
    private static $tournamentStatus;
    private static $message;

    public function __construct($parameters = null) {
        !empty($parameters[TOURNAMENT_ID])      && self::setTournamentId($parameters[TOURNAMENT_ID]);
        self::setTournamentStatus($parameters[STATUS]);
        !empty($parameters[MESSAGE])            && self::setMessage($parameters[MESSAGE]);
    }

    public static function get() {
        return array(
            TOURNAMENT_ID => self::getTournamentId(),
            STATUS => self::getTournamentStatus(),
            MESSAGE => self::getMessage()
        );
    }

    public static function getTournamentId() { return self::$tournamentId; }

    public static function setTournamentId($tournamentId): void { self::$tournamentId = $tournamentId; }

    public static function getTournamentStatus() { return self::$tournamentStatus; }

    public static function setTournamentStatus($tournamentStatus = 0): void { self::$tournamentStatus = $tournamentStatus; }

    public static function getMessage() { return self::$message; }

    public static function setMessage($message): void { self::$message = $message; }

    public static function prepareTournamentMessage($gameName, $startDate, $status) {
        $date = CustomDate::getDateFromMilliseconds($startDate);
        $message = '';
        if ($status === TOURNAMENT_STATUS_OPEN) {
            $message = $date . ' tarihli ' . $gameName . ' turnuvasına kayıtlandın.';
        } else if ($status === TOURNAMENT_STATUS_ACTIVE) {
            $message = $date . ' tarihli ' . $gameName . ' turnuvası başladı, rakiplerini ve fikstürü takip et.';
        } else if ($status === TOURNAMENT_STATUS_CLOSE) {
            $message = $date . ' tarihli ' . $gameName . ' turnuvası sonuçlandı.';
        }
        return $message;
    }

}
