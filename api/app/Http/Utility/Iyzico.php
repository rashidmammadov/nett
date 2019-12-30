<?php

namespace App\Http\Utility;

use App\Http\Queries\MySQL\ApiQuery;
use Illuminate\Support\Facades\Log;
use Iyzipay\Model;
use Iyzipay\Options;
use Iyzipay\Request\CreateSubMerchantRequest;

class Iyzico {

    private $options;

    public function __construct() {
        $this->setOptions();
    }

    public function getOptions() { return $this->options; }

    public function setOptions(): void {
        $this->options = new Options();
        $this->options->setApiKey(env('IYZICO_API_KEY'));
        $this->options->setSecretKey(env('IYZICO_SECRET_KEY'));
        $this->options->setBaseUrl(env('IYZICO_BASE_URL'));
    }

    /**
     * Set new sub merchant for iyzico account and save on db.
     *
     * @param Merchant $merchant - holds the merchant data.
     * @param $user - holds the user data.
     * @return string | null subMerchantKey
     */
    public function setIyzicoSubMerchant(Merchant $merchant, $user) {
        $request = new CreateSubMerchantRequest();
        $request->setLocale(Model\Locale::TR);
        $request->setConversationId($user[USERNAME]);
        $request->setSubMerchantExternalId($user[IDENTIFIER]);
        $request->setSubMerchantType($merchant->getMerchantType());
        $request->setAddress($user[ADDRESS]);
        $request->setTaxOffice($merchant->getTaxOffice());
        $request->setLegalCompanyTitle($merchant->getCompanyTitle());
        $request->setContactName($user[NAME]);
        $request->setContactSurname($user[SURNAME]);
        $request->setEmail($user[EMAIL]);
        $request->setGsmNumber($user[PHONE]);
        $request->setName($user[USERNAME]);
        $request->setIban($merchant->getIban());
        $request->setIdentityNumber($merchant->getIdentityNumber());
        $request->setCurrency(Model\Currency::TL);

        $subMerchant = Model\SubMerchant::create($request, $this->getOptions());
        if ($subMerchant->getErrorCode()) {
            Log::error('IYZICO: ' . $subMerchant->getErrorMessage());
        } else {
            ApiQuery::setMerchantKey($user[IDENTIFIER], $subMerchant->getSubMerchantKey());
        }
        return $subMerchant->getSubMerchantKey();
    }

}
