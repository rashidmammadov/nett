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

    private $financeId;
    private $referenceCode;
    private $userId;
    private $type;
    private $channel;
    private $tournamentId;
    private $iban;
    private $amount;
    private $amountWithCommission;
    private $ticket;
    private $status;

    public function __construct($parameters = null) {
        $this->setReferenceCode($parameters[REFERENCE_CODE]);
        $this->setUserId($parameters[USER_ID]);
        $this->setType($parameters[TYPE]);
        $this->setChannel($parameters[CHANNEL]);
        $this->setTournamentId($parameters[TOURNAMENT_ID]);
        $this->setIban($parameters[IBAN]);
        $this->setAmount($parameters[AMOUNT]);
        $this->setAmountWithCommission($parameters[AMOUNT_WITH_COMMISSION]);
        $this->setTicket($parameters[TICKET]);
        $this->setStatus($parameters[STATUS]);
    }

    public function get() {
        return array(
            FINANCE_ID => $this->getFinanceId(),
            REFERENCE_CODE => $this->getReferenceCode(),
            USER_ID => $this->getUserId(),
            TYPE => $this->getType(),
            CHANNEL => $this->getChannel(),
            TOURNAMENT_ID => $this->getTournamentId(),
            IBAN => $this->getIban(),
            AMOUNT => $this->getAmount(),
            AMOUNT_WITH_COMMISSION => $this->getAmountWithCommission(),
            TICKET => $this->getTicket(),
            STATUS => $this->getStatus()
        );
    }

    public function getFinanceId() { return $this->financeId; }

    public function setFinanceId($financeId): void { $this->financeId = $financeId; }

    public function getReferenceCode() { return $this->referenceCode; }

    public function setReferenceCode($referenceCode): void { $this->referenceCode = $referenceCode; }

    public function getUserId() { return $this->userId; }

    public function setUserId($userId): void { $this->userId = $userId; }

    public function getType() { return $this->type; }

    public function setType($type): void { $this->type = $type; }

    public function getChannel() { return $this->channel; }

    public function setChannel($channel): void { $this->channel = $channel; }

    public function getTournamentId() { return $this->tournamentId; }

    public function setTournamentId($tournamentId): void { $this->tournamentId = $tournamentId; }

    public function getIban() { return $this->iban; }

    public function setIban($iban): void { $this->iban = $iban; }

    public function getAmount() { return $this->amount; }

    public function setAmount($amount): void { $this->amount = $amount; }

    public function getAmountWithCommission() { return $this->amountWithCommission; }

    public function setAmountWithCommission($amountWithCommission): void { $this->amountWithCommission = $amountWithCommission; }

    public function getTicket() { return $this->ticket; }

    public function setTicket($ticket): void { $this->ticket = $ticket; }

    public function getStatus() { return $this->status; }

    public function setStatus($status): void { $this->status = $status; }

    /**
     * Generate reference code for financial operation. Reference Code format should be includes
     * channel type (for DEPOSIT: "D", for TOURNAMENT: "T", for WITHDRAW: "W"), user id and current date as milliseconds.
     *
     * @param string $channel - the types of finance channel
     * @param int $userId - the id of user
     * @return string - reference code of operation
     */
    public function generateReferenceCode(string $channel, int $userId): string {
        $referenceCode = '';
        if ($channel == DEPOSIT) {
            $referenceCode .= 'D';
        } else if ($channel == TOURNAMENT) {
            $referenceCode .= 'T';
        } else if ($channel == WITHDRAW) {
            $referenceCode .= 'W';
        }
        $referenceCode .=  $userId . '-' .CustomDate::getDateFromMilliseconds();
        return $referenceCode;
    }

    /**
     * Prepare participant`s earnings data for save finance table.
     *
     * @param integer $tournamentId - holds the tournament id.
     * @param array $participants - holds the participant.
     * @param double $participationFee - the participation fee of tournament.
     */
    public function setKnockOutFixtureParticipantsEarnings($tournamentId, $participants, $participationFee) {
        foreach ($participants as $participant) {
            $finance = new Finance();
            $referenceCode = $finance->generateReferenceCode(TOURNAMENT, $participant[PARTICIPANT_ID]);
            $finance->setReferenceCode($referenceCode);
            $finance->setUserId($participant[PARTICIPANT_ID]);
            $finance->setType(PLAYER);
            $finance->setChannel(TOURNAMENT);
            $finance->setTournamentId($tournamentId);
            list($amount, $ticket) = Fixture::calculateKnockOutWinnersEarnings(count($participants),
                $participationFee, $participant[TOURNAMENT_RANKING]);
            $finance->setAmount($amount);
            $finance->setTicket($ticket);
            $finance->setStatus(FINANCE_STATUS_WAITING);
            if ($finance->getAmount() > 0 || $finance->getTicket() > 0) {
                ApiQuery::setFinance($finance->get());
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
    public function setKnockOutFixtureHolderEarnings($tournamentId, $holderId, $participantsCount, $participationFee) {
        $finance = new Finance();
        $referenceCode = $finance->generateReferenceCode(TOURNAMENT, $holderId);
        $finance->setReferenceCode($referenceCode);
        $finance->setUserId($holderId);
        $finance->setType(HOLDER);
        $finance->setChannel(TOURNAMENT);
        $finance->setTournamentId($tournamentId);
        $earning = Fixture::calculateKnockOutHolderEarnings($participantsCount, $participationFee);
        $finance->setAmount($earning);
        $finance->setTicket(0);
        $finance->setStatus(FINANCE_STATUS_WAITING);
        if ($finance->getAmount() > 0) {
            ApiQuery::setFinance($finance->get());
        }
    }

}
