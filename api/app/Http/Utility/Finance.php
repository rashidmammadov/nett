<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 09.05.2019
 * Time: 17:53
 */

namespace App\Http\Utility;

use App\Http\Queries\MySQL\ApiQuery;

class Finance {

    private static $financeId;
    private static $referenceCode;
    private static $userId;
    private static $type;
    private static $channel;
    private static $tournamentId;
    private static $iban;
    private static $amount;
    private static $amountWithCommission;
    private static $ticket;
    private static $status;

    public function __construct($parameters = null) {
        self::setReferenceCode($parameters[REFERENCE_CODE]);
        self::setUserId($parameters[USER_ID]);
        self::setType($parameters[TYPE]);
        self::setChannel($parameters[CHANNEL]);
        self::setTournamentId($parameters[TOURNAMENT_ID]);
        self::setIban($parameters[IBAN]);
        self::setAmount($parameters[AMOUNT]);
        self::setAmountWithCommission($parameters[AMOUNT_WITH_COMMISSION]);
        self::setTicket($parameters[TICKET]);
        self::setStatus($parameters[STATUS]);
    }

    public static function get() {
        return array(
            FINANCE_ID => self::getFinanceId(),
            REFERENCE_CODE => self::getReferenceCode(),
            USER_ID => self::getUserId(),
            TYPE => self::getType(),
            CHANNEL => self::getChannel(),
            TOURNAMENT_ID => self::getTournamentId(),
            IBAN => self::getIban(),
            AMOUNT => self::getAmount(),
            AMOUNT_WITH_COMMISSION => self::getAmountWithCommission(),
            TICKET => self::getTicket(),
            STATUS => self::getStatus()
        );
    }

    public static function getFinanceId() { return self::$financeId; }

    public static function setFinanceId($financeId): void { self::$financeId = $financeId; }

    public static function getReferenceCode() { return self::$referenceCode; }

    public static function setReferenceCode($referenceCode): void { self::$referenceCode = $referenceCode; }

    public static function getUserId() { return self::$userId; }

    public static function setUserId($userId): void { self::$userId = $userId; }

    public static function getType() { return self::$type; }

    public static function setType($type): void { self::$type = $type; }

    public static function getChannel() { return self::$channel; }

    public static function setChannel($channel): void { self::$channel = $channel; }

    public static function getTournamentId() { return self::$tournamentId; }

    public static function setTournamentId($tournamentId): void { self::$tournamentId = $tournamentId; }

    public static function getIban() { return self::$iban; }

    public static function setIban($iban): void { self::$iban = $iban; }

    public static function getAmount() { return self::$amount; }

    public static function setAmount($amount): void { self::$amount = $amount; }

    public static function getAmountWithCommission() { return self::$amountWithCommission; }

    public static function setAmountWithCommission($amountWithCommission): void { self::$amountWithCommission = $amountWithCommission; }

    public static function getTicket() { return self::$ticket; }

    public static function setTicket($ticket): void { self::$ticket = $ticket; }

    public static function getStatus() { return self::$status; }

    public static function setStatus($status): void { self::$status = $status; }

    /**
     * Prepare participant`s earnings data for save finance table.
     *
     * @param integer $tournamentId - holds the tournament id.
     * @param array $participants - holds the participant.
     * @param double $participationFee - the participation fee of tournament.
     */
    public static function setKnockOutFixtureParticipantsEarnings($tournamentId, $participants, $participationFee) {
        foreach ($participants as $participant) {
            $finance = new Finance();
            $finance::setUserId($participant[PARTICIPANT_ID]);
            $finance::setType(PLAYER);
            $finance::setChannel(TOURNAMENT);
            $finance::setTournamentId($tournamentId);
            list($amount, $ticket) = Fixture::calculateKnockOutWinnersEarnings(count($participants),
                $participationFee, $participant[TOURNAMENT_RANKING]);
            $finance::setAmount($amount);
            $finance::setTicket($ticket);
            $finance::setStatus(FINANCE_STATUS_WAITING);
            if ($finance::getAmount() > 0 || $finance::getTicket() > 0) {
                ApiQuery::setFinance($finance::get());
            }
        }
    }

    /**
     * Prepare holder`s earnings data for save finance table.
     *
     * @param integer $tournamentId - holds the tournament id.
     * @param integer $holderId - holds the tournament`s holder id.
     * @param integer $participantsCount - holds the tournament`s participants count.
     * @param double $participationFee - the participation fee of tournament.
     */
    public static function setKnockOutFixtureHolderEarnings($tournamentId, $holderId, $participantsCount, $participationFee) {
        $finance = new Finance();
        $finance::setUserId($holderId);
        $finance::setType(HOLDER);
        $finance::setChannel(TOURNAMENT);
        $finance::setTournamentId($tournamentId);
        $earning = Fixture::calculateKnockOutHolderEarnings($participantsCount, $participationFee);
        $finance::setAmount($earning);
        $finance::setTicket(0);
        $finance::setStatus(FINANCE_STATUS_WAITING);
        if ($finance::getAmount() > 0) {
            ApiQuery::setFinance($finance::get());
        }
    }

}
