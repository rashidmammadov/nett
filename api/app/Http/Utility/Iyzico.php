<?php

namespace App\Http\Utility;

use App\Http\Queries\MySQL\ApiQuery;
use Illuminate\Support\Facades\Log;
use Iyzipay\Model;
use Iyzipay\Options;
use Iyzipay\Request\CreatePaymentRequest;
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

    public function payment(Merchant $merchant, $user) {
        $request = new CreatePaymentRequest();
        $request->setLocale(Model\Locale::TR);
        $request->setConversationId("123456789");
        $request->setPrice("1");
        $request->setPaidPrice("1.2");
        $request->setCurrency(Model\Currency::TL);
        $request->setInstallment(1);
        $request->setBasketId("B67832");
        $request->setPaymentGroup(Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl("https://www.merchant.com/callback");

        $paymentCard = new Model\PaymentCard();
        $paymentCard->setCardHolderName("John Doe");
        $paymentCard->setCardNumber("5528790000000008");
        $paymentCard->setExpireMonth("12");
        $paymentCard->setExpireYear("2030");
        $paymentCard->setCvc("123");
        $paymentCard->setRegisterCard(0);
        $request->setPaymentCard($paymentCard);

        $buyer = new Model\Buyer();
        $buyer->setId($user[IDENTIFIER]);
        $buyer->setName($user[NAME]);
        $buyer->setSurname($user[SURNAME]);
        $buyer->setGsmNumber("+90" . $user[PHONE]);
        $buyer->setEmail($user[EMAIL]);
        $buyer->setIdentityNumber($merchant->getIdentityNumber());
//        $buyer->setLastLoginDate("2015-10-05 12:43:35");
//        $buyer->setRegistrationDate("2013-04-21 15:12:09");
        $buyer->setRegistrationAddress($user[ADDRESS]);
        $buyer->setIp("85.34.78.112"); // TODO: find ip of user.
        $buyer->setCity($user[CITY]);
        $buyer->setCountry($user[COUNTRY]);
//        $buyer->setZipCode("34732");
        $request->setBuyer($buyer);

        $billingAddress = new Model\Address();
        $billingAddress->setContactName($user[NAME] . '' . $user[SURNAME]);
        $billingAddress->setCity($user[CITY]);
        $billingAddress->setCountry($user[COUNTRY]);
        $billingAddress->setAddress($user[ADDRESS]);
//        $billingAddress->setZipCode("34742");
        $request->setBillingAddress($billingAddress);

        $basketItem = new Model\BasketItem();
        $basketItem->setId("BI102");
        $basketItem->setName("Participation Fee");
        $basketItem->setCategory1("Budget");
        $basketItem->setItemType(Model\BasketItemType::VIRTUAL);
        $basketItem->setPrice("1");
        $basketItems[0] = $basketItem;
        $request->setBasketItems($basketItems);

        $checkoutFormInitialize = Model\ThreedsInitialize::create($request, $this->getOptions());
        Log::info($checkoutFormInitialize->getStatus());
        Log::info($checkoutFormInitialize->getErrorMessage());
        Log::info($checkoutFormInitialize->getHtmlContent());
    }

    /**
     * Set new sub merchant for iyzico account and save on db.
     *
     * @param Merchant $merchant - holds the merchant data.
     * @param $user - holds the user data.
     * @return string | null subMerchantKey
     */
    public function setIyzicoSubMerchant(Merchant $merchant, $user) {
        $request = $this->subMerchant($merchant, $user);
        $subMerchant = Model\SubMerchant::create($request, $this->getOptions());
        if ($subMerchant->getErrorCode()) {
            Log::error('IYZICO: ' . $subMerchant->getErrorMessage());
        } else {
            ApiQuery::setMerchantKey($user[IDENTIFIER], $subMerchant->getSubMerchantKey());
        }
        return $subMerchant->getSubMerchantKey();
    }

    /**
     * Update exist sub merchant for iyzico account and save on db.
     *
     * @param Merchant $merchant - holds the merchant data.
     * @param $user - holds the user data.
     * @return string | null subMerchantKey
     */
    public function updateIyzicoSubMerchant(Merchant $merchant, $user) {
        $request = $this->subMerchant($merchant, $user);
        $subMerchant = Model\SubMerchant::create($request, $this->getOptions());
        if ($subMerchant->getErrorCode()) {
            Log::error('IYZICO: ' . $subMerchant->getErrorMessage());
        } else {
            ApiQuery::setMerchantKey($user[IDENTIFIER], $subMerchant->getSubMerchantKey());
        }
        return $subMerchant->getSubMerchantKey();
    }

    /**
     * @param Merchant $merchant
     * @param $user
     * @return CreateSubMerchantRequest
     */
    private function subMerchant(Merchant $merchant, $user): CreateSubMerchantRequest {
        $request = new CreateSubMerchantRequest();
        $request->setLocale(Model\Locale::TR);
        $request->setConversationId($user[USERNAME]);
        $request->setSubMerchantExternalId($user[IDENTIFIER]);
        $request->setSubMerchantType($merchant->getMerchantType());
        $request->setAddress($user[ADDRESS]);
        $request->setTaxOffice($merchant->getTaxOffice());
        $request->setTaxNumber($merchant->getTaxNumber());
        $request->setLegalCompanyTitle($merchant->getCompanyTitle());
        $request->setContactName($user[NAME]);
        $request->setContactSurname($user[SURNAME]);
        $request->setEmail($user[EMAIL]);
        $request->setGsmNumber($user[PHONE]);
        $request->setName($user[USERNAME]);
        $request->setIban($merchant->getIban());
        $request->setIdentityNumber($merchant->getIdentityNumber());
        $request->setCurrency(Model\Currency::TL);
        return $request;
    }

}
