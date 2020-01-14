<?php

namespace App\Http\Utility;

class FinanceReport {

    private $label;
    private $amount;
    private $currency;

    public function __construct($parameters = null) {
        $this->setLabel($parameters[LABEL]);
        $this->setAmount($parameters[AMOUNT]);
        $this->setCurrency($parameters[CURRENCY]);
    }

    public function get() {
        return array(
            LABEL => $this->getLabel(),
            AMOUNT => $this->getAmount(),
            CURRENCY => $this->getCurrency()
        );
    }

    public function getLabel() { return $this->label; }

    public function setLabel($label): void { $this->label = $label; }

    public function getAmount() { return $this->amount; }

    public function setAmount($amount): void { $this->amount = number_format($amount, 2, '.', ''); }

    public function getCurrency() { return $this->currency; }

    public function setCurrency($currency): void { $this->currency = $currency; }

}
