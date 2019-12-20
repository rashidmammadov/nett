<?php

namespace App\Http\Utility;

class FinanceArchive {

    private static $amount;
    private static $channel;
    private static $referenceCode;
    private static $status;
    private static $date;

    public function __construct($parameters = null) {
        self::setAmount($parameters[AMOUNT]);
        self::setChannel($parameters[CHANNEL]);
        self::setReferenceCode($parameters[REFERENCE_CODE]);
        self::setStatus($parameters[STATUS]);
        self::setDate($parameters[DATE]);
    }

    public static function get() {
        return array(
            AMOUNT => self::getAmount(),
            CHANNEL => self::getChannel(),
            REFERENCE_CODE => self::getReferenceCode(),
            STATUS => self::getStatus(),
            DATE => self::getDate()
        );
    }

    public static function getAmount() { return self::$amount; }

    public static function setAmount($amount): void { self::$amount = $amount; }

    public static function getChannel() { return self::$channel; }

    public static function setChannel($channel): void { self::$channel = $channel; }

    public static function getReferenceCode() { return self::$referenceCode; }

    public static function setReferenceCode($referenceCode): void { self::$referenceCode = $referenceCode; }

    public static function getStatus() { return self::$status; }

    public static function setStatus($status): void { self::$status = $status; }

    public static function getDate() { return self::$date; }

    public static function setDate($date): void { self::$date = $date; }

}
