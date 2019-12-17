<?php

namespace App\Http\Controllers;

use App\Http\Queries\MySQL\ApiQuery;
use App\Http\Utility\CustomDate;
use App\Http\Utility\Fixture;
use App\Http\Utility\Match;
use App\Repository\Transformers\ParticipantTransformer;
use App\Repository\Transformers\TournamentTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class FinanceController extends ApiController {

    public function __construct() {}

    public function get(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $userId = $user[IDENTIFIER];
            $pageNo = !empty($request[PAGE_NO]) ? $request[PAGE_NO] : null;
            $itemPerPage = !empty($request[ITEM_PER_PAGE]) ? $request[ITEM_PER_PAGE] : null;
            $data = ApiQuery::getFinanceWithPagination($userId, $pageNo, $itemPerPage);
            return $this->respondCreated('', $data);
        } catch(JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
        }
    }

}
