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

    /**
     * Confirm payment after 3D Secure is success
     *
     * @param $response - the response from 3D Secure option
     * @return string
     */
    public function confirmPayment($response) {
        $request = new CreateThreedsPaymentRequest();
        $request->setLocale(Model\Locale::TR);
        $request->setConversationId($response['conversationId']);
        $request->setPaymentId($response['paymentId']);
        $request->setConversationData($response['conversationData']);
        $message = $this->mdStatusMessage($response['mdStatus']);

        $threedsPayment = Model\ThreedsPayment::create($request, $this->getOptions());
        if ($threedsPayment->getErrorCode()) {
            Log::error('IYZICO: ' . $threedsPayment->getErrorMessage());
            return '<div align="center" style="width: 100%; height: calc(100% - 64px); background: #fbfbfb; padding: 32px 0; font-family: Ubuntu, sans-serif;">
                <h3 style="color: #f44336;">Hatalı</h3>
                <h2 style="color: #303030; font-weight: 100;">' . $message .'</h2>
                </div>';
        } else {
            $userId = $response['conversationId'];
            $user = ApiQuery::getUserById($userId);
            if ($user) {
                $paymentItem = $threedsPayment->getPaymentItems()[0];
                $finance = new Finance();
                $finance->setReferenceCode($paymentItem->getPaymentTransactionId());
                $finance->setUserId($userId);
                $finance->setType($user[TYPE]);
                $finance->setChannel(DEPOSIT);
                $finance->setAmount($threedsPayment->getPrice());
                $finance->setAmountWithCommission($paymentItem->getPaidPrice());
                $finance->setStatus(FINANCE_STATUS_APPROVED);
                $user[BUDGET] = number_format($user[BUDGET] + $threedsPayment->getPrice(), 2, '.', '');
                ApiQuery::setFinance($finance->get());
                ApiQuery::updateUserBudget($userId, $user[BUDGET]);
                return '<div align="center" style="width: 100%; height: calc(100% - 64px); background: #fbfbfb; padding: 32px 0; font-family: Ubuntu, sans-serif;">
                    <h3 style="color: #5FDC96;">Başarılı</h3>
                    <h2 style="color: #303030; font-weight: 100;">Para Yatırma İşlemi Sonuçlandı<br/>Eklenen Tutar ' . ($threedsPayment->getPrice()) . '₺</h2>
                    </div>';
            }
        }
    }

    /**
     * Make payment with 3D Secure and return confirm page
     *
     * @param Merchant $merchant - holds the merchant info
     * @param $user - holds the user data
     * @param $card - holds the card info
     * @param $ipAddress - holds the ip address of client
     * @return string | null htmlContent
     */
    public function make3DSPayment(Merchant $merchant, $user, $card, $ipAddress) {
        $request = $this->payment($user, $card);

        $paymentCard = $this->paymentCard($card);
        $request->setPaymentCard($paymentCard);

        $buyer = $this->buyer($merchant, $user, $ipAddress);
        $request->setBuyer($buyer);

        $billingAddress = $this->billingAddress($user);
        $request->setBillingAddress($billingAddress);

        $basketItem = $this->basketItem($card);
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
     * Set new sub merchant for iyzico account and save on db
     *
     * @param Merchant $merchant - holds the merchant data
     * @param $user - holds the user data
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
     * @param $card
     * @return Model\BasketItem
     */
    private function basketItem($card): Model\BasketItem {
        $basketItem = new Model\BasketItem();
        $basketItem->setId("PF-" . $card[PRICE]);
        $basketItem->setName("Participation Fee");
        $basketItem->setCategory1("Budget");
        $basketItem->setItemType(Model\BasketItemType::VIRTUAL);
        $basketItem->setPrice($card[PRICE]);
        return $basketItem;
    }

    /**
     * @param $user
     * @return Model\Address
     */
    private function billingAddress($user): Model\Address {
        $billingAddress = new Model\Address();
        $billingAddress->setContactName($user[NAME] . '' . $user[SURNAME]);
        $billingAddress->setCity($user[CITY]);
        $billingAddress->setCountry($user[COUNTRY]);
        $billingAddress->setAddress($user[ADDRESS]);
        return $billingAddress;
    }

    /**
     * @param Merchant $merchant
     * @param $user
     * @param $ipAddress
     * @return Model\Buyer
     */
    private function buyer(Merchant $merchant, $user, $ipAddress): Model\Buyer {
        $buyer = new Model\Buyer();
        $buyer->setId($user[IDENTIFIER]);
        $buyer->setName($user[NAME]);
        $buyer->setSurname($user[SURNAME]);
        $buyer->setGsmNumber("+90" . $user[PHONE]);
        $buyer->setEmail($user[EMAIL]);
        $buyer->setIdentityNumber($merchant->getIdentityNumber());
        $buyer->setRegistrationAddress($user[ADDRESS]);
        $buyer->setIp($ipAddress);
        $buyer->setCity($user[CITY]);
        $buyer->setCountry($user[COUNTRY]);
        return $buyer;
    }

    /**
     * @param $user
     * @param $card
     * @return CreatePaymentRequest
     */
    private function payment($user, $card): CreatePaymentRequest {
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
        return $request;
    }

    /**
     * @param $card
     * @return Model\PaymentCard
     */
    private function paymentCard($card): Model\PaymentCard {
        $paymentCard = new Model\PaymentCard();
        $paymentCard->setCardHolderName($card[CARD_HOLDER_NAME]);
        $paymentCard->setCardNumber($card[CARD_NUMBER]);
        $paymentCard->setExpireMonth($card[EXPIRE_MONTH]);
        $paymentCard->setExpireYear($card[EXPIRE_YEAR]);
        $paymentCard->setCvc($card[CVC]);
        $paymentCard->setRegisterCard(0);
        return $paymentCard;
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

    private function mdStatusMessage($mdStatus) {
        $message = '';
        if (number_format($mdStatus) == 0) {
            $message = '3-D Secure imzası geçersiz veya doğrulama';
        } else if (number_format($mdStatus) == 2) {
            $message = 'Kart sahibi veya bankası sisteme kayıtlı değil';
        } else if (number_format($mdStatus) == 3) {
            $message = 'Kartın bankası sisteme kayıtlı değil';
        } else if (number_format($mdStatus) == 4) {
            $message = 'Doğrulama denemesi, kart sahibi sisteme daha sonra kayıt olmayı seçmiş';
        } else if (number_format($mdStatus) == 5) {
            $message = 'Doğrulama yapılamıyor';
        } else if (number_format($mdStatus) == 6) {
            $message = '3-D Secure hatası';
        } else if (number_format($mdStatus) == 7) {
            $message = 'Sistem hatası';
        } else if (number_format($mdStatus) == 8) {
            $message = 'Bilinmeyen kart no';
        }
        return $message;
    }

}
