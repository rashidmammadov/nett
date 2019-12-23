<?php

namespace App\Http\Controllers;

use App\Http\Queries\MySQL\ApiQuery;
use App\Http\Utility\CustomDate;
use App\Http\Utility\FinanceArchive;
use App\Http\Utility\Fixture;
use App\Http\Utility\Match;
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
                $financeArchive::setAmount($item[AMOUNT]);
                $financeArchive::setChannel($item[CHANNEL]);
                $financeArchive::setReferenceCode($item[REFERENCE_CODE]);
                $financeArchive::setStatus($item[STATUS]);
                $financeArchive::setDate(CustomDate::convertDateToMillisecond($item['updated_at']));
                array_push($data, $financeArchive::get());
            }
            return $this->respondWithPagination('', $pagination, $data);
        } else {
            return $this->respondWithError(SOMETHING_WRONG_WITH_DB);
        }
    }

}
