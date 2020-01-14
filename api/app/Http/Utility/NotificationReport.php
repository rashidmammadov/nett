<?php

namespace App\Http\Utility;

class NotificationReport {

    private $tournamentId;
    private $status;
    private $message;

    public function __construct($parameters = null) {
        $this->setTournamentId($parameters[TOURNAMENT_ID]);
        $this->setStatus($parameters[STATUS]);
        $this->setMessage($parameters[MESSAGE]);
    }

    public function get() {
        return array(
            TOURNAMENT_ID => $this->getTournamentId(),
            STATUS => $this->getStatus(),
            MESSAGE => $this->getMessage()
        );
    }

    public function getTournamentId() { return $this->tournamentId; }

    public function setTournamentId($tournamentId): void { $this->tournamentId = $tournamentId; }

    public function getStatus() { return $this->status; }

    public function setStatus($status = 0): void { $this->status = $status; }

    public function getMessage() { return $this->message; }

    public function setMessage($message): void { $this->message = $message; }

    public function prepareTournamentMessage($gameName, $startDate, $status, $type) {
        $date = CustomDate::getDateFromMilliseconds($startDate);
        $message = '<b>' . $date . '</b> tarihli <b>' . $gameName . '</b>';
        if ($type == HOLDER) {
            if ($status === TOURNAMENT_STATUS_OPEN) {
                $message =  $message . ' turnuvasını başlattın.';
            } else if ($status === TOURNAMENT_STATUS_ACTIVE) {
                $message = $message . ' turnuvası başladı, turnuva katılımıcılarını kontrol etmeyi ve skorları zamanında girmeyi unutma.';
            } else if ($status === TOURNAMENT_STATUS_CANCEL) {
                $message = $message . ' turnuvası yeterli katılımcı sayısına ulaşmadığı için veya başka bir sebepten dolayı iptal edildi.';
            } else if ($status === TOURNAMENT_STATUS_CLOSE) {
                $message = $message . ' turnuvası sonuçlandı.';
            }
        } else if ($type == PLAYER) {
            if ($status === TOURNAMENT_STATUS_OPEN) {
                $message =  $message . ' turnuvasına kayıtlandın.';
            } else if ($status === TOURNAMENT_STATUS_ACTIVE) {
                $message = $message . ' turnuvası başladı, rakiplerini ve fikstürü takip et.';
            } else if ($status === TOURNAMENT_STATUS_CLOSE) {
                $message = $message . ' turnuvası sonuçlandı.';
            }
        }
        return $message;
    }

}
