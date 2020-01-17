<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 06.05.2019
 * Time: 15:46
 */

namespace App\Http\Utility;

use App\Http\Queries\MySQL\ApiQuery;

class Participant {

    private $tournamentId;
    private $participantId;
    private $paymentAmount;
    private $paymentType;
    private $earnings;
    private $tournamentRanking;
    private $point;
    private $referenceCode;

    public function __construct($parameters = null) {
        $this->setTournamentId($parameters[TOURNAMENT_ID]);
        $this->setParticipantId($parameters[PARTICIPANT_ID]);
        $this->setPaymentAmount($parameters[PAYMENT_AMOUNT]);
        $this->setPaymentType($parameters[PAYMENT_TYPE]);
        $this->setEarnings($parameters[EARNINGS]);
        $this->setTournamentRanking($parameters[TOURNAMENT_RANKING]);
        $this->setPoint($parameters[POINT]);
        $this->setReferenceCode($parameters[REFERENCE_CODE]);
    }

    public function getParticipant() {
        return array(
            TOURNAMENT_ID => $this->getTournamentId(),
            PARTICIPANT_ID => $this->getParticipantId(),
            PAYMENT_AMOUNT => $this->getPaymentAmount(),
            PAYMENT_TYPE => $this->getPaymentType(),
            EARNINGS => $this->getEarnings(),
            TOURNAMENT_RANKING => $this->getTournamentRanking(),
            POINT => $this->getPoint(),
            REFERENCE_CODE => $this->getReferenceCode()
        );
    }

    public function getParticipantId() { return $this->participantId; }

    public function setParticipantId($participantId): void { $this->participantId = $participantId; }

    public function getPaymentAmount() { return $this->paymentAmount; }

    public function setPaymentAmount($paymentAmount): void { $this->paymentAmount = $paymentAmount; }

    public function getPaymentType() { return $this->paymentType; }

    public function setPaymentType($paymentType): void { $this->paymentType = $paymentType; }

    public function getEarnings() { return $this->earnings; }

    public function setEarnings($earnings): void { $this->earnings = $earnings; }

    public function getTournamentId() { return $this->tournamentId; }

    public function setTournamentId($tournamentId): void { $this->tournamentId = $tournamentId; }

    public function getTournamentRanking() { return $this->tournamentRanking; }

    public function setTournamentRanking($tournamentRanking): void { $this->tournamentRanking = $tournamentRanking; }

    public function getPoint() { return $this->point; }

    public function setPoint($point): void { $this->point = $point; }

    public function getReferenceCode() { return $this->referenceCode; }

    public function setReferenceCode($referenceCode): void { $this->referenceCode = $referenceCode; }

    /**
     * @description: update participant`s point
     * @param integer $tournamentId - tournament id
     * @param integer $participantId - participant id
     * @param integer $point - holds the user`s goal count
     * @param integer $coefficient - holds the coefficient of tour.
     */
    public function calculatePlayerPoint($tournamentId, $participantId, $point, $coefficient) {
        $participantQuery = ApiQuery::getParticipants($tournamentId, $participantId)->first();
        $participant = new Participant($participantQuery);
        $total = $coefficient + $point;
        $oldPoint = $participant->getPoint();
        $newPoint = null;
        if (is_null($oldPoint)) {
            $newPoint = $total;
        } else {
            $newPoint = $oldPoint + $total;
        }
        $participant->setPoint($newPoint);
        ApiQuery::updateParticipantPoint($tournamentId, $participantId, $participant->getPoint());
    }

    /**
     * @description: update participant`s earnings and ranking
     * @param $tournamentId - tournament id
     * @param $rankings - the list of fixture rankings
     */
    public function setKnockOutFixtureRanking($tournamentId, $rankings) {
        foreach ($rankings as $ranking) {
            $participant = new Participant();
            $participant->setTournamentId($tournamentId);
            $participant->setParticipantId($ranking[PARTICIPANT_ID]);
            $participant->setTournamentRanking($ranking[TOURNAMENT_RANKING]);
            ApiQuery::updateParticipantRanking($participant->getTournamentId(), $participant->getParticipantId(),
                $participant->getTournamentRanking());
        }
    }

}
