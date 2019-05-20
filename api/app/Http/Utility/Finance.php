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
    private static $userId;
    private static $type;
    private static $channel;
    private static $tournamentId;
    private static $amount;
    private static $ticket;
    private static $status;

    public function __construct($parameters = null) {
        !empty($parameters[USER_ID])        && self::setUserId($parameters[USER_ID]);
        !empty($parameters[TYPE])           && self::setType($parameters[TYPE]);
        !empty($parameters[CHANNEL])        && self::setChannel($parameters[CHANNEL]);
        !empty($parameters[TOURNAMENT_ID])  && self::setTournamentId($parameters[TOURNAMENT_ID]);
        !empty($parameters[AMOUNT])         && self::setAmount($parameters[AMOUNT]);
        !empty($parameters[TICKET])         && self::setTicket($parameters[TICKET]);
        !empty($parameters[STATUS])         && self::setStatus($parameters[STATUS]);
    }

    public static function getFinance() {
        return array(
            FINANCE_ID => self::getFinanceId(),
            USER_ID => self::getUserId(),
            TYPE => self::getType(),
            CHANNEL => self::getChannel(),
            TOURNAMENT_ID => self::getTournamentId(),
            AMOUNT => self::getAmount(),
            TICKET => self::getTicket(),
            STATUS => self::getStatus()
        );
    }

    public static function getFinanceId() { return self::$financeId; }

    public static function setFinanceId($financeId): void { self::$financeId = $financeId; }

    public static function getUserId() { return self::$userId; }

    public static function setUserId($userId): void { self::$userId = $userId; }

    public static function getType() { return self::$type; }

    public static function setType($type): void { self::$type = $type; }

    public static function getChannel() { return self::$channel; }

    public static function setChannel($channel): void { self::$channel = $channel; }

    public static function getTournamentId() { return self::$tournamentId; }

    public static function setTournamentId($tournamentId): void { self::$tournamentId = $tournamentId; }

    public static function getAmount() { return self::$amount; }

    public static function setAmount($amount): void { self::$amount = $amount; }

    public static function getTicket() { return self::$ticket; }

    public static function setTicket($ticket): void { self::$ticket = $ticket; }

    public static function getStatus() { return self::$status; }

    public static function setStatus($status): void { self::$status = $status; }

    /**
     * @description prepare participant`s earnings data for save finance table.
     * @param integer $tournamentId - holds the tournament id
     * @param array $rankings - holds the participant`s ranking
     */
    public static function setKnockOutFixtureParticipantsEarnings($tournamentId, $rankings) {
        foreach ($rankings as $ranking) {
            $finance = new Finance();
            $finance::setUserId($ranking[PARTICIPANT_ID]);
            $finance::setType(PLAYER);
            $finance::setChannel(TOURNAMENT);
            $finance::setTournamentId($tournamentId);
            $result = Fixture::calculateKnockOutParticipantEarnings($ranking[TOURNAMENT_RANKING], count($rankings));
            $finance::setAmount($result[EARNINGS]);
            $finance::setTicket($result[TICKET]);
            $finance::setStatus(FINANCE_STATUS_WAITING);
            if ($finance::getAmount() > 0 || $finance::getTicket() > 0) {
                ApiQuery::setFinance($finance::getFinance());
            }
        }
    }

    /**
     * @description prepare holder`s earnings data for save finance table.
     * @param integer $tournamentId - holds the tournament id
     * @param integer $holderId - holds the tournament`s holder id
     * @param integer $participantsCount - holds the tournament`s participants count
     */
    public static function setKnockOutFixtureHolderEarnings($tournamentId, $holderId, $participantsCount) {
        $finance = new Finance();
        $finance::setUserId($holderId);
        $finance::setType(HOLDER);
        $finance::setChannel(TOURNAMENT);
        $finance::setTournamentId($tournamentId);
        $earning = Fixture::calculateKnockOutHolderEarning($participantsCount);
        $finance::setAmount($earning);
        $finance::setTicket(0);
        $finance::setStatus(FINANCE_STATUS_WAITING);
        if ($finance::getAmount() > 0) {
            ApiQuery::setFinance($finance::getFinance());
        }
    }

}
