<?php

namespace App\Http\Utility;

class FinanceArchive {

    private $amount;
    private $channel;
    private $referenceCode;
    private $status;
    private $date;

    public function __construct($parameters = null) {
        $this->setAmount($parameters[AMOUNT]);
        $this->setChannel($parameters[CHANNEL]);
        $this->setReferenceCode($parameters[REFERENCE_CODE]);
        $this->setStatus($parameters[STATUS]);
        $this->setDate($parameters[DATE]);
    }

    public function get() {
        return array(
            AMOUNT => $this->getAmount(),
            CHANNEL => $this->getChannel(),
            REFERENCE_CODE => $this->getReferenceCode(),
            STATUS => $this->getStatus(),
            DATE => $this->getDate()
        );
    }

    public function getAmount() { return $this->amount; }

    public function setAmount($amount): void { $this->amount = $amount; }

    public function getChannel() { return $this->channel; }

    public function setChannel($channel): void { $this->channel = $channel; }

    public function getReferenceCode() { return $this->referenceCode; }

    public function setReferenceCode($referenceCode): void { $this->referenceCode = $referenceCode; }

    public function getStatus() { return $this->status; }

    public function setStatus($status): void { $this->status = $status; }

    public function getDate() { return $this->date; }

    public function setDate($date): void { $this->date = $date; }

}
