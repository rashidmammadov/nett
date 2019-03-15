<?php

namespace App\Http\Queries\MySQL;

use App\Fixture;
use App\Participant;
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

    /** -------------------- PARTICIPANT QUERIES -------------------- **/

    /**
     * @description query to attend participant to given tournament
     * @param integer $tournamentId
     * @param integer $participantId
     * @param array $parameters
     */
    public static function attendTournament($tournamentId, $participantId, $parameters) {
        Participant::create([
            TOURNAMENT_ID => $tournamentId,
            PARTICIPANT_ID => $participantId,
            PAYMENT_AMOUNT => $parameters[PAYMENT_AMOUNT],
            PAYMENT_TYPE => $parameters[PAYMENT_TYPE]
        ]);
    }

    /**
     * @description query to check if user already registered for tournament
     * @param integer $tournamentId
     * @param integer $participantId
     * @return mixed $queryResult
     */
    public static function checkIfAttended($tournamentId, $participantId) {
        $queryResult = Participant::where([
            [TOURNAMENT_ID, EQUAL_SIGN, $tournamentId],
            [PARTICIPANT_ID, EQUAL_SIGN, $participantId]
        ])->exists();

        return $queryResult;
    }

    /**
     * @description query to get all participants if $participantId is null
     *      or get given $participantId
     * @param integer $tournamentId
     * @param integer $participantId
     * @return mixed $queryResult
     */
    public static function getParticipants($tournamentId, $participantId = null) {
        $queryResult = Participant::where(TOURNAMENT_ID, EQUAL_SIGN, $tournamentId)
            ->where(function ($query) use ($participantId) {
                if ($participantId) {
                    $query->where(PARTICIPANT_ID, EQUAL_SIGN, $participantId);
                }
            })->get();

        return $queryResult;
    }

    /**
     * @description query to remove participant from tournament
     * @param integer $tournamentId
     * @param integer $participantId
     */
    public static function removeParticipant($tournamentId, $participantId) {
        Participant::where([
            [TOURNAMENT_ID, EQUAL_SIGN, $tournamentId],
            [PARTICIPANT_ID, EQUAL_SIGN, $participantId]
        ])->delete();
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

    /**
     * @description query to get tournament general detail
     * @param integer $tournamentId
     * @return mixed
     */
    public static function getTournament($tournamentId) {
        $queryResult = Tournament::where([
            [TOURNAMENT_ID, EQUAL_SIGN, $tournamentId]
        ])->first();

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
     * @description query to get user by id.
     * @param $userId
     * @return mixed
     */
    public static function getUserById($userId) {
        $queryResult = User::where([
            [IDENTIFIER, EQUAL_SIGN, $userId]
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
