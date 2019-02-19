<?php

namespace App\Http\Queries\MySQL;

use App\User;

class ApiQuery {

    /**
     * @description query to check if email exist.
     * @param string $email
     * @return mixed
     */
    public static function checkEmail($email) {
        $queryResult = User::where([
            [EMAIL, EQUAL_SIGN, $email]
        ])->exists();

        return $queryResult;
    }

    /**
     * @description query to check if username exist.
     * @param string $username
     * @return mixed
     */
    public static function checkUsername($username) {
        $queryResult = User::where([
            [USERNAME, EQUAL_SIGN, $username]
        ])->exists();

        return $queryResult;
    }

    /**
     * @description query to get user by email.
     * @param string $email
     * @return mixed
     */
    public static function getUserByEmail($email) {
        $queryResult = User::where([
            [EMAIL, EQUAL_SIGN, $email]
        ])->first();

        return $queryResult;
    }

    /**
     * @description query to create new user.
     * @param mixed $user
     * @return mixed
     */
    public static function setUser($user) {
        User::create([
            TYPE => $user[TYPE],
            USERNAME => $user[USERNAME],
            EMAIL => $user[EMAIL],
            PASSWORD => \Hash::make($user[PASSWORD]),
            CITY => $user[CITY],
            DISTRICT => $user[DISTRICT]
        ]);
    }
}
