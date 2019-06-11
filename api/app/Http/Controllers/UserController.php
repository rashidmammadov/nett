<?php

namespace App\Http\Controllers;

use App\Http\Queries\MySQL\ApiQuery;
use App\Repository\Transformers\UserTransformer;
use Illuminate\Http\Request;
use \Illuminate\Http\Response as Res;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Validator;


class UserController extends ApiController {

    private $userTransformer;

    public function __construct(UserTransformer $userTransformer) {
        $this->userTransformer = $userTransformer;
    }

    /**
     * @description: refresh user by token
     * @return mixed: User info
     */
    public function refreshUser() {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return $this->respondCreated("Get User", $this->userTransformer->transform($user));
        } catch (TokenExpiredException $e){
            $refreshedToken = JWTAuth::refresh(JWTAuth::getToken());
            $user = JWTAuth::setToken($refreshedToken)->toUser();
            $user->remember_token = $refreshedToken;
            $user->save();
            return $this->respondCreated("Token Refreshed", $this->userTransformer->transform($user));
        } catch (JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * @description: authorized user to login.
     * @param Request $request
     * @return mixed
     */
    public function auth(Request $request) {
        $rules = array (
            EMAIL => 'required|email',
            PASSWORD => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
        } else {
            $user = ApiQuery::getUserByEmail($request[EMAIL]);
            if ($user) {
                /** add one signal device id **/
                if ($request[ONESIGNAL_DEVICE_ID]) {
                    $user[ONESIGNAL_DEVICE_ID] = $request[ONESIGNAL_DEVICE_ID];
                    $user->save();
                }

                $remember_token = $user->remember_token;
                if ($remember_token == NULL) {
                    return $this->login($request, false);
                }

                try {
                    $user = JWTAuth::toUser($remember_token);
                    return $this->respondCreated(LOGGED_IN_SUCCESSFULLY, $this->userTransformer->transform($user));
                } catch (JWTException $e) {
                    $user->remember_token = NULL;
                    $user->save();
                    $this->setStatusCode($e->getStatusCode());
                    return $this->respondWithError($e->getMessage());
                }
            } else {
                return $this->respondWithError(INVALID_EMAIL_OR_PASSWORD);
            }
        }
    }

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
                        $request[PICTURE] = $this->addRandomAvatar();
                        ApiQuery::setUser($request);
                        return $this->login($request, true);
                    }
                }
            }
        }
    }

    /**
     * @description handle request to activate user.
     * @param Request $request
     * @return mixed
     */
    public function activate(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $rules = array(
                NAME => 'required',
                SURNAME => 'required',
                PHONE => 'required',
                BIRTHDAY => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                $data = ApiQuery::updateUser($user[IDENTIFIER], $request);
                return $this->respondCreated(USER_ACTIVATED, $this->userTransformer->transform($data));
            }
        } catch (JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            return $this->respondWithError($e->getMessage());
        }

    }

    /**
     * @description: logout user and clear token.
     * @return mixed
     */
    public function logout() {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $user->remember_token = NULL;
            $user->save();
            $this->setStatusCode(Res::HTTP_OK);
            return $this->respondCreated(LOGGED_OUT_SUCCESSFULLY);
        } catch(JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * @description: login user if exist.
     * @param Request $request
     * @param boolean $newUser
     * @return mixed
     */
    private function login($request, $newUser) {
        $credentials = [EMAIL => $request[EMAIL], PASSWORD => $request[PASSWORD]];
        if ( ! $token = JWTAuth::attempt($credentials)) {
            return $this->respondWithError(USER_DOES_NOT_EXIST);
        }

        $user = JWTAuth::toUser($token);
        $user->remember_token = $token;
        $user->save();

        if ($newUser) {
            // TODO: send email
        }

        return $this->respondCreated(LOGGED_IN_SUCCESSFULLY, $this->userTransformer->transform($user));
    }

    /**
     * @description: set random avatar for user.
     * @return mixed
     */
    private function addRandomAvatar() {
        $imageUrl = env('HOST_NAME') . env('AVATARS_PATH') . rand(MIN_AVATAR_COUNT, MAX_AVATAR_COUNT) . '.png';
        return $imageUrl;
    }

}
