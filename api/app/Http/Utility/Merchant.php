<?php

namespace App\Http\Utility;

class Merchant {

    private $merchantId;
    private $merchantType;
    private $merchantKey;
    private $identityNumber;
    private $taxOffice;
    private $taxNumber;
    private $companyTitle;
    private $iban;

    public function __construct($parameters = null) {
        $this->setMerchantId($parameters[MERCHANT_ID]);
        $this->setMerchantType($parameters[MERCHANT_TYPE]);
        $this->setMerchantKey($parameters[MERCHANT_KEY]);
        $this->setIdentityNumber($parameters[IDENTITY_NUMBER]);
        $this->setTaxOffice($parameters[TAX_OFFICE]);
        $this->setTaxNumber($parameters[TAX_NUMBER]);
        $this->setCompanyTitle($parameters[COMPANY_TITLE]);
        $this->setIban($parameters[IBAN]);
    }

    public function get() {
        return array(
            MERCHANT_ID => $this->getMerchantId(),
            MERCHANT_TYPE => $this->getMerchantType(),
            MERCHANT_KEY => $this->getMerchantKey(),
            IDENTITY_NUMBER => $this->getIdentityNumber(),
            TAX_OFFICE => $this->getTaxOffice(),
            TAX_NUMBER => $this->getTaxNumber(),
            COMPANY_TITLE => $this->getCompanyTitle(),
            IBAN => $this->getIban()
        );
    }

    public function getMerchantId() { return $this->merchantId; }

    public function setMerchantId($merchantId): void { $this->merchantId = $merchantId; }

    public function getMerchantType() { return $this->merchantType; }

    public function setMerchantType($merchantType): void { $this->merchantType = $merchantType; }

    public function getMerchantKey() { return $this->merchantKey; }

    public function setMerchantKey($merchantKey): void { $this->merchantKey = $merchantKey; }

    public function getIdentityNumber() { return $this->identityNumber; }

    public function setIdentityNumber($identityNumber): void { $this->identityNumber = $identityNumber; }

    public function getTaxOffice() { return $this->taxOffice; }

    public function setTaxOffice($taxOffice): void { $this->taxOffice = $taxOffice; }

    public function getTaxNumber() { return $this->taxNumber; }

    public function setTaxNumber($taxNumber): void { $this->taxNumber = $taxNumber; }

    public function getCompanyTitle() { return $this->companyTitle; }

    public function setCompanyTitle($companyTitle): void { $this->companyTitle = $companyTitle; }

    public function getIban() { return $this->iban; }

    public function setIban($iban): void { $this->iban = $iban; }

}
