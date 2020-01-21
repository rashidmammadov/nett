<?php

namespace App\Http\Controllers;

use App\Http\Queries\MySQL\ApiQuery;
use App\Http\Utility\Email;
use App\Http\Utility\Iyzico;
use App\Http\Utility\Merchant;
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
     * Refresh user by token
     * @return mixed: User info
     */
    public function refreshUser() {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $merchant = new Merchant(ApiQuery::getMerchant($user[IDENTIFIER]));
            $user[MERCHANT] = $merchant->get();
            return $this->respondCreated("Get User", $this->userTransformer->transform($user));
        } catch (TokenExpiredException $e) {
            $refreshedToken = JWTAuth::refresh(JWTAuth::getToken());
            $user = JWTAuth::setToken($refreshedToken)->toUser();
            if ($user[IDENTIFIER]) {
                $user->remember_token = $refreshedToken;
                $user->save();
                $merchant = new Merchant(ApiQuery::getMerchant($user[IDENTIFIER]));
                $user[MERCHANT] = $merchant->get();
            }
            return $this->respondCreated("Token Refreshed", $this->userTransformer->transform($user));
        } catch (JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
        }
    }

    /**
     * Authorized user to login.
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
                    $merchant = new Merchant(ApiQuery::getMerchant($user[IDENTIFIER]));
                    $user[MERCHANT] = $merchant->get();
                    return $this->respondCreated(LOGGED_IN_SUCCESSFULLY, $this->userTransformer->transform($user));
                } catch (JWTException $e) {
                    $user->remember_token = NULL;
                    $user->save();
                    $this->setStatusCode($e->getStatusCode());
                    $this->setMessage(AUTHENTICATION_ERROR);
                    return $this->respondWithError($this->getMessage());
                }
            } else {
                return $this->respondWithError(INVALID_EMAIL_OR_PASSWORD);
            }
        }
    }

    /**
     * Handle request to create new user.
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
                        $queryResult = ApiQuery::setUser($request);
                        if ($queryResult) {
                            return $this->login($request, true);
                        } else {
                            return $this->respondWithError(SOMETHING_WRONG_WITH_DB);
                        }
                    }
                }
            }
        }
    }

    /**
     * Handle request to reset password of user with given email.
     * @param Request $request - hold the user`s email address.
     * @return mixed
     */
    public function resetPassword(Request $request) {
        $rules = array (
            EMAIL => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
        } else {
            $checkEmail = ApiQuery::checkEmail($request[EMAIL]);
            if (!$checkEmail) {
                return $this->respondWithError(USER_DOES_NOT_EXIST);
            } else {
                $emailResult = $this->setNewPassword($request[EMAIL]);
                if ($emailResult) {
                    return $this->respondCreated(PASSWORD_SEND_TO_MAIL);
                } else {
                    return $this->respondWithError(SOMETHING_WRONG_WITH_EMAIL);
                }
            }
        }
    }

    /**
     * Handle request to activate user.
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
                BIRTHDAY => 'required',
                MERCHANT_TYPE => 'required',
                ADDRESS => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                $data = ApiQuery::activateUser($user[IDENTIFIER], $request);
                /** set iyzico sub merchant data */
                $merchant = new Merchant(ApiQuery::setMerchant($user[IDENTIFIER], $request));
                $merchant->setMerchantId($user[IDENTIFIER]);
                $iyzico = new Iyzico();
                $merchantKey = $iyzico->setIyzicoSubMerchant($merchant, $user);
                $merchant->setMerchantKey($merchantKey);
                $data[MERCHANT] = $merchant->get();
                return $this->respondCreated(USER_ACTIVATED, $this->userTransformer->transform($data));
            }
        } catch (JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
        }
    }

    /**
     * Logout user and clear token.
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
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
        }
    }

    /**
     * Update password of user.
     * @param Request $request
     * @return mixed
     */
    public function updatePassword(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $rules = array(
                PASSWORD => 'required',
                PASSWORD_CONFIRMATION => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                if ($request[PASSWORD] != $request[PASSWORD_CONFIRMATION]) {
                    return $this->respondWithError(PASSWORD_VALIDATION_FAILED);
                } else {
                    ApiQuery::updateUserPassword($user[EMAIL], $request[PASSWORD]);
                    $user->remember_token = NULL;
                    $user->save();
                    return $this->respondCreated(PASSWORD_UPDATED_SUCCESSFULLY);
                }
            }
        } catch(JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
        }
    }

    /**
     * Update merchant data of user.
     * @param Request $request
     * @return mixed
     */
    public function updateMerchant(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $rules = array(
                IBAN => 'required',
                IDENTITY_NUMBER => 'required',
                MERCHANT_TYPE => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->respondValidationError(FIELDS_VALIDATION_FAILED, $validator->errors());
            } else {
                $merchantQueryResult = ApiQuery::updateMerchant($user[IDENTIFIER], $request);
                if ($merchantQueryResult) {
                    $merchant = new Merchant($merchantQueryResult);
                    $iyzico = new Iyzico();
                    $merchantKey = $iyzico->updateIyzicoSubMerchant($merchant, $user);
                    $merchant->setMerchantKey($merchantKey);
                    return $this->respondCreated(MERCHANT_UPDATED_SUCCESSFULLY, $merchant->get());
                } else {
                    $this->respondWithError(SOMETHING_WRONG_WITH_DB);
                }
            }
        } catch(JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
        }
    }

    /**
     * Update settings of user.
     * @param Request $request
     * @return mixed
     */
    public function updateSettings(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $user = ApiQuery::updateUserSettings($user[IDENTIFIER], $request);
            return $this->respondCreated(SETTINGS_UPDATED_SUCCESSFULLY, $user);
        } catch(JWTException $e) {
            $this->setStatusCode($e->getStatusCode());
            $this->setMessage(AUTHENTICATION_ERROR);
            return $this->respondWithError($this->getMessage());
        }
    }

    /**
     * Login user if exist.
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
            Email::send(WELCOME_EMAIL, $request);
        }
        $merchant = new Merchant(ApiQuery::getMerchant($user[IDENTIFIER]));
        $user[MERCHANT] = $merchant->get();
        return $this->respondCreated(LOGGED_IN_SUCCESSFULLY, $this->userTransformer->transform($user));
    }

    /**
     * Set random avatar for user.
     * @return mixed
     */
    private function addRandomAvatar() {
        $imageUrl = env('HOST_NAME') . env('AVATARS_PATH') . rand(MIN_AVATAR_COUNT, MAX_AVATAR_COUNT) . '.png';
        return $imageUrl;
    }

    private function setNewPassword($email) {
        $lowerCases = 'abcdefghijklmnopqrstuvwxyz';
        $upperCases = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digits = '1234567890';
        $newPassword = $this->randomKeys($upperCases, 2) . $this->randomKeys($digits, 2) . $this->randomKeys($lowerCases, 2);
        ApiQuery::updateUserPassword($email, $newPassword);
        $data = array(
            EMAIL => $email,
            PASSWORD => $newPassword
        );
        return Email::send(RESET_PASSWORD, $data);
    }

    private function randomKeys($keys, $limit) {
        $pass = array();
        $keyLength = strlen($keys) - 1;
        for ($i = 0; $i < $limit; $i++) {
            $n = rand(0, $keyLength);
            $pass[] = $keys[$n];
        }
        return implode($pass);
    }

}
