<?php

namespace App\Http\Controllers;

use App\Http\Queries\MySQL\ApiQuery;
use Illuminate\Http\Request;
use Validator;


class UserController extends ApiController {

    public function __construct() {}

    /**
     * @description handle request to create new user.
     * @param Request $request
     * @return mixed
     */
    public function register(Request $request) {
        $rules = array (
            USERNAME => 'required',
            EMAIL => 'required',
            TYPE => 'required',
            PASSWORD => 'required|min:6',
            PASSWORD_CONFIRMATION => 'required|min:6',
            CITY => 'required',
            DISTRICT => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
        } else {
            $checkEmail = ApiQuery::checkEmail($request[EMAIL]);
            if ($checkEmail) {
                return $this->respondWithError(THIS_EMAIL_ALREADY_EXIST);
            } else {
                $checkUsername = ApiQuery::checkUsername($request[USERNAME]);
                if ($checkUsername) {
                    return $this->respondWithError(THIS_USERNAME_ALREADY_EXIST);
                } else {
                    if ($request[PASSWORD] != $request[PASSWORD_CONFIRMATION]) {
                        return $this->respondWithError(PASSWORD_VALIDATION_FAILED);
                    } else {
                        ApiQuery::setUser($request);
                        return $this->respondCreated(USER_REGISTERED_SUCCESSFULLY);
                    }
                }
            }
        }
    }

}
