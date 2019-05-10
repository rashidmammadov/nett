<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 06.05.2019
 * Time: 15:46
 */

namespace App\Http\Utility;

use App\Http\Queries\MySQL\ApiQuery;
use Illuminate\Support\Facades\Log;

class Participant {

    private static $tournamentId;
    private static $participantId;
    private static $paymentAmount;
    private static $paymentType;
    private static $earnings;
    private static $tournamentRanking;
    private static $point;
    private static $referenceCode;

    public function __construct($parameters = null) {
        !empty($parameters[TOURNAMENT_ID])      && self::setTournamentId($parameters[TOURNAMENT_ID]);
        !empty($parameters[PARTICIPANT_ID])     && self::setParticipantId($parameters[PARTICIPANT_ID]);
        !empty($parameters[PAYMENT_AMOUNT])     && self::setPaymentAmount($parameters[PAYMENT_AMOUNT]);
        !empty($parameters[PAYMENT_TYPE])       && self::setPaymentType($parameters[PAYMENT_TYPE]);
        !empty($parameters[EARNINGS])           && self::setEarnings($parameters[EARNINGS]);
        !empty($parameters[TOURNAMENT_RANKING]) && self::setTournamentRanking($parameters[TOURNAMENT_RANKING]);
        !empty($parameters[POINT])              && self::setPoint($parameters[POINT]);
        !empty($parameters[REFERENCE_CODE])     && self::setReferenceCode($parameters[REFERENCE_CODE]);
    }

    public static function getParticipant() {
        return array(
            TOURNAMENT_ID => self::getTournamentId(),
            PARTICIPANT_ID => self::getParticipantId(),
            PAYMENT_AMOUNT => self::getPaymentAmount(),
            PAYMENT_TYPE => self::getPaymentType(),
            EARNINGS => self::getEarnings(),
            TOURNAMENT_RANKING => self::getTournamentRanking(),
            POINT => self::getPoint(),
            REFERENCE_CODE => self::getReferenceCode()
        );
    }

    public static function getParticipantId() { return self::$participantId; }

    public static function setParticipantId($participantId): void { self::$participantId = $participantId; }

    public static function getPaymentAmount() { return self::$paymentAmount; }

    public static function setPaymentAmount($paymentAmount): void { self::$paymentAmount = $paymentAmount; }

    public static function getPaymentType() { return self::$paymentType; }

    public static function setPaymentType($paymentType): void { self::$paymentType = $paymentType; }

    public static function getEarnings() { return self::$earnings; }

    public static function setEarnings($earnings): void { self::$earnings = $earnings; }

    public static function getTournamentId() { return self::$tournamentId; }

    public static function setTournamentId($tournamentId): void { self::$tournamentId = $tournamentId; }

    public static function getTournamentRanking() { return self::$tournamentRanking; }

    public static function setTournamentRanking($tournamentRanking): void { self::$tournamentRanking = $tournamentRanking; }

    public static function getPoint() { return self::$point; }

    public static function setPoint($point): void { self::$point = $point; }

    public static function getReferenceCode() { return self::$referenceCode; }

    public static function setReferenceCode($referenceCode): void { self::$referenceCode = $referenceCode; }

    /**
     * @description: update participant`s point
     * @param $tournamentId - tournament id
     * @param $participantId - participant id
     * @param $point - holds the user`s goal count
     * @param $winner - if player is winner
     */
    public static function calculatePlayerPoint($tournamentId, $participantId, $point, $winner) {
        $query = ApiQuery::getParticipants($tournamentId, $participantId)->first();
        $participant = new Participant($query);
        $total = $point;
        if ($winner == true) {
            $total += 10;
        };

        $oldPoint = $participant::getPoint();
        $newPoint = null;
        if (is_null($oldPoint)) {
            $newPoint = $total;
        } else {
            $newPoint = $oldPoint + $total;
        }
        $participant::setPoint($newPoint);
        ApiQuery::updateParticipantPoint($tournamentId, $participantId, $newPoint);
    }

    /**
     * @description: update participant`s earnings and ranking
     * @param $tournamentId - tournament id
     * @param $rankings - the list of fixture rankings
     */
    public static function setKnockOutFixtureRanking($tournamentId, $rankings) {
        foreach ($rankings as $ranking) {
            $participant = new Participant();
            $participant::setTournamentId($tournamentId);
            $participant::setParticipantId($ranking[PARTICIPANT_ID]);
            $participant::setTournamentRanking($ranking[TOURNAMENT_RANKING]);
            ApiQuery::updateParticipantRanking($participant::getTournamentId(), $participant::getParticipantId(), $participant::getTournamentRanking());
        }
    }

}
