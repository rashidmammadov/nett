<?php

namespace App\Http\Queries\MySQL;

use App\Fixture;
use App\Tournament;
use App\User;

class ApiQuery {

    /** -------------------- FIXTURE QUERIES -------------------- **/

    /**
     * @description query to create new tournament`s fixture
     * @param $tournamentId
     * @param $fixture
     */
    public static function setFixture($tournamentId, $fixture) {
        Fixture::create([
            TOURNAMENT_ID => $tournamentId,
            FIXTURE => json_encode($fixture, JSON_UNESCAPED_UNICODE)
        ]);
    }

    /** -------------------- TOURNAMENT QUERIES -------------------- **/

    /**
     * @description query to create new tournament
     * @param $tournament
     * @return mixed
     */
    public static function setTournament($tournament) {
        $queryResult =  Tournament::create([
            HOLDER_ID => $tournament[HOLDER_ID],
            GAME_ID => $tournament[GAME_ID],
            TOURNAMENT_TYPE => $tournament[TOURNAMENT_TYPE],
            PARTICIPANT_COUNT => $tournament[PARTICIPANT_COUNT],
            START_DATE => $tournament[START_DATE],
            STATUS => $tournament[STATUS],
            DAYS => $tournament[DAYS]
        ]);

        return $queryResult;
    }

    /** -------------------- USER QUERIES -------------------- **/

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
