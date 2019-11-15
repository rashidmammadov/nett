<?php


namespace App\Http\Utility;


class FinanceReport {

    private static $label;
    private static $amount;
    private static $currency;

    public function __construct($parameters = null) {
        !empty($parameters[LABEL])      && self::setLabel($parameters[LABEL]);
        !empty($parameters[AMOUNT])     && self::setAmount($parameters[AMOUNT]);
        !empty($parameters[CURRENCY])   && self::setCurrency($parameters[CURRENCY]);
    }

    public static function get() {
        return array(
            LABEL => self::getLabel(),
            AMOUNT => self::getAmount(),
            CURRENCY => self::getCurrency()
        );
    }

    public static function getLabel() { return self::$label; }

    public static function setLabel($label): void { self::$label = $label; }

    public static function getAmount() { return self::$amount; }

    public static function setAmount($amount): void { self::$amount = $amount; }

    public static function getCurrency() { return self::$currency; }

    public static function setCurrency($currency): void { self::$currency = $currency; }

}
