<?php

namespace App\Http\Controllers;

use App\Http\Queries\MySQL\ApiQuery;
use App\Http\Utility\CustomDate;
use App\Http\Utility\Finance;
use App\Http\Utility\FinanceArchive;
use App\Http\Utility\Fixture;
use App\Http\Utility\Iyzico;
use App\Http\Utility\Match;
use App\Http\Utility\Merchant;
use App\Repository\Transformers\ParticipantTransformer;
use App\Repository\Transformers\TournamentTransformer;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class FinanceController extends ApiController {

    public function __construct() {}

    public function confirmDeposit(Request $request) {
        $iyzico = new Iyzico();
        return $iyzico->confirmPayment($request);
    }

    public function deposit(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $rules = array(
                PRICE => 'required',
                PAID_PRICE => 'required',
                CARD_NUMBER => 'required',
                CARD_HOLDER_NAME => 'required',
                EXPIRE_MONTH => 'required',
                EXPIRE_YEAR => 'required',
                CVC => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                if ($request[PRICE] >= 15 && $request[PRICE] <= 1500) {
                    $ipAddress = $request->getClientIp();
                    $merchant = new Merchant(ApiQuery::getMerchant($user[IDENTIFIER]));
                    $iyzico = new Iyzico();
                    $threeDS = $iyzico->make3DSPayment($merchant, $user, $request, $ipAddress);
                    if ($threeDS) {
                        return $this->respondCreated('3D Secure Confirm Page', $threeDS);
                    } else {
                        return $this->respondWithError(PAYMENT_VALIDATION_ERROR);
                    }
                } else {
                    $this->respondWithError(PRICE_MUST_BETWEEN_LIMIT);
                }
            }
        } catch(JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
        }
    }

    public function get(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $rules = array(
                PAGE => 'required'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                $itemPerPage = !empty($request[ITEM_PER_PAGE]) ? $request[ITEM_PER_PAGE] : 50;
                return $this->getFinanceArchiveWithPagination($user[IDENTIFIER], $itemPerPage);
            }
        } catch(JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
        }
    }

    public function withdraw(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $rules = array(
                IBAN => 'required',
                WITHDRAWAL_AMOUNT => 'required|numeric|min:50'
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                if ($request[WITHDRAWAL_AMOUNT] <= $user[BUDGET]) {
                    $finance = new Finance();
                    $referenceCode = $finance->generateReferenceCode(WITHDRAW, $user[IDENTIFIER]);
                    $finance->setReferenceCode($referenceCode);
                    $finance->setUserId($user[IDENTIFIER]);
                    $finance->setType($user[TYPE]);
                    $finance->setChannel(WITHDRAW);
                    $finance->setIban($request[IBAN]);
                    $finance->setAmount($request[WITHDRAWAL_AMOUNT]);
                    $finance->setStatus(FINANCE_STATUS_ORDER);
                    $financeQueryResult = ApiQuery::setFinance($finance->get());
                    if ($financeQueryResult) {
                        $newBudget = number_format((float)($user[BUDGET] - $request[WITHDRAWAL_AMOUNT]), 2, '.', '');
                        $userQueryResult = ApiQuery::updateUserBudget($user[IDENTIFIER], $newBudget);
                        if ($financeQueryResult && $userQueryResult) {
                            return $this->respondCreated(SUCCESSFUL_WITHDRAW_ORDER, array(BUDGET => $newBudget));
                        } else {
                            return $this->respondWithError(SOMETHING_WRONG_WITH_DB);
                        }
                    } else {
                        return $this->respondWithError(SOMETHING_WRONG_WITH_DB);
                    }
                } else {
                    return $this->respondWithError(INSUFFICIENT_BALANCE);
                }
            }
        } catch(JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
        }
    }

    /**
     * Fetch finance data history with pagination.
     * @param integer $userId - the id of user.
     * @param integer $itemPerPage - the number of element on each pagination bulk.
     * @return mixed - if data exist returns response data else returns error message.
     */
    private function getFinanceArchiveWithPagination($userId, $itemPerPage) {
        $pagination = ApiQuery::getFinanceWithPagination($userId, $itemPerPage);
        if ($pagination) {
            $data = array();
            foreach ($pagination->items() as $item) {
                $financeArchive = new FinanceArchive();
                $financeArchive->setAmount($item[AMOUNT]);
                $financeArchive->setChannel($item[CHANNEL]);
                $financeArchive->setReferenceCode($item[REFERENCE_CODE]);
                $financeArchive->setStatus($item[STATUS]);
                $financeArchive->setDate(CustomDate::convertDateToMillisecond($item['updated_at']));
                array_push($data, $financeArchive->get());
            }
            return $this->respondWithPagination('', $pagination, $data);
        } else {
            return $this->respondWithError(SOMETHING_WRONG_WITH_DB);
        }
    }

}
