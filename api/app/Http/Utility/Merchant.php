<?php

namespace App\Http\Utility;

class Merchant {

    private static $merchantId;
    private static $merchantType;
    private static $merchantKey;
    private static $identityNumber;
    private static $taxOffice;
    private static $companyTitle;
    private static $iban;

    public function __construct($parameters = null) {
        self::setMerchantId($parameters[MERCHANT_ID]);
        self::setMerchantType($parameters[MERCHANT_TYPE]);
        self::setMerchantKey($parameters[MERCHANT_KEY]);
        self::setIdentityNumber($parameters[IDENTITY_NUMBER]);
        self::setTaxOffice($parameters[TAX_OFFICE]);
        self::setCompanyTitle($parameters[COMPANY_TITLE]);
        self::setIban($parameters[IBAN]);
    }

    public static function get() {
        return array(
            MERCHANT_ID => self::getMerchantId(),
            MERCHANT_TYPE => self::getMerchantType(),
            MERCHANT_KEY => self::getMerchantKey(),
            IDENTITY_NUMBER => self::getIdentityNumber(),
            TAX_OFFICE => self::getTaxOffice(),
            COMPANY_TITLE => self::getCompanyTitle(),
            IBAN => self::getIban()
        );
    }

    public static function getMerchantId() { return self::$merchantId; }

    public static function setMerchantId($merchantId): void { self::$merchantId = $merchantId; }

    public static function getMerchantType() { return self::$merchantType; }

    public static function setMerchantType($merchantType): void { self::$merchantType = $merchantType; }

    public static function getMerchantKey() { return self::$merchantKey; }

    public static function setMerchantKey($merchantKey): void { self::$merchantKey = $merchantKey; }

    public static function getIdentityNumber() { return self::$identityNumber; }

    public static function setIdentityNumber($identityNumber): void { self::$identityNumber = $identityNumber; }

    public static function getTaxOffice() { return self::$taxOffice; }

    public static function setTaxOffice($taxOffice): void { self::$taxOffice = $taxOffice; }

    public static function getCompanyTitle() { return self::$companyTitle; }

    public static function setCompanyTitle($companyTitle): void { self::$companyTitle = $companyTitle; }

    public static function getIban() { return self::$iban; }

    public static function setIban($iban): void { self::$iban = $iban; }

}
