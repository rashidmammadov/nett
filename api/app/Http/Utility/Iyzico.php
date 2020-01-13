<?php

namespace App\Http\Utility;

use App\Http\Queries\MySQL\ApiQuery;
use Illuminate\Support\Facades\Log;
use Iyzipay\Model;
use Iyzipay\Options;
use Iyzipay\Request\CreatePaymentRequest;
use Iyzipay\Request\CreateSubMerchantRequest;
use Iyzipay\Request\CreateThreedsPaymentRequest;

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

    public function confirmPayment($response) {
        $request = new CreateThreedsPaymentRequest();
        $request->setLocale(Model\Locale::TR);
        $request->setConversationId($response['conversationId']);
        $request->setPaymentId($response['paymentId']);
        $request->setConversationData($response['conversationData']);

        $threedsPayment = Model\ThreedsPayment::create($request, $this->getOptions());
        if ($threedsPayment->getErrorCode()) {
            Log::error('IYZICO: ' . $threedsPayment->getErrorMessage());
        } else {
            ApiQuery::updateUserBudget($response['conversationId'], $threedsPayment->getPrice());
            return $threedsPayment->getPaidPrice();
        }
    }

    public function payment(Merchant $merchant, $user, $card) {
        $request = new CreatePaymentRequest();
        $request->setLocale(Model\Locale::TR);
        $request->setConversationId($user[IDENTIFIER]);
        $request->setPrice($card[PRICE]);
        $request->setPaidPrice($card[PAID_PRICE]);
        $request->setCurrency(Model\Currency::TL);
        $request->setInstallment(1);
        $request->setBasketId("NO_BASKET_ID");
        $request->setPaymentGroup(Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl(env('HOST_NAME') . 'api/v1/confirmDeposit');

        $paymentCard = new Model\PaymentCard();
        $paymentCard->setCardHolderName($card[CARD_HOLDER_NAME]);
        $paymentCard->setCardNumber($card[CARD_NUMBER]);//5528790000000008
        $paymentCard->setExpireMonth($card[EXPIRE_MONTH]);//12
        $paymentCard->setExpireYear($card[EXPIRE_YEAR]);//2030
        $paymentCard->setCvc($card[CVC]);
        $paymentCard->setRegisterCard(0);
        $request->setPaymentCard($paymentCard);

        $buyer = new Model\Buyer();
        $buyer->setId($user[IDENTIFIER]);
        $buyer->setName($user[NAME]);
        $buyer->setSurname($user[SURNAME]);
        $buyer->setGsmNumber("+90" . $user[PHONE]);
        $buyer->setEmail($user[EMAIL]);
        $buyer->setIdentityNumber($merchant->getIdentityNumber());
        $buyer->setRegistrationAddress($user[ADDRESS]);
        $buyer->setIp("85.34.78.112"); // TODO: find ip of user.
        $buyer->setCity($user[CITY]);
        $buyer->setCountry($user[COUNTRY]);
        $request->setBuyer($buyer);

        $billingAddress = new Model\Address();
        $billingAddress->setContactName($user[NAME] . '' . $user[SURNAME]);
        $billingAddress->setCity($user[CITY]);
        $billingAddress->setCountry($user[COUNTRY]);
        $billingAddress->setAddress($user[ADDRESS]);
        $request->setBillingAddress($billingAddress);

        $basketItem = new Model\BasketItem();
        $basketItem->setId("PF-" . $card[PRICE]);
        $basketItem->setName("Participation Fee");
        $basketItem->setCategory1("Budget");
        $basketItem->setItemType(Model\BasketItemType::VIRTUAL);
        $basketItem->setPrice($card[PRICE]);
        $basketItems[0] = $basketItem;
        $request->setBasketItems($basketItems);

        $threeDSInitialize = Model\ThreedsInitialize::create($request, $this->getOptions());
        if ($threeDSInitialize->getErrorCode()) {
            Log::error('IYZICO: ' . $threeDSInitialize->getErrorMessage());
        } else {
            return $threeDSInitialize->getHtmlContent();
        }
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
