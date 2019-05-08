<?php

namespace App\Http\Queries\MySQL;

use App\Fixture;
use App\Game;
use App\Participant;
use App\Tournament;
use App\User;
use Illuminate\Support\Facades\Log;

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

    /**
     * @description query to get given tournament`s fixture
     * @param $tournamentId
     * @return mixed
     */
    public static function getFixture($tournamentId) {
        $queryResult = Fixture::where(TOURNAMENT_ID, EQUAL_SIGN, $tournamentId)->first();
        return $queryResult;
    }

    /**
     * @description query to update given tournament`s fixture
     * @param $tournamentId
     * @param $fixture
     * @return mixed
     */
    public static function updateFixture($tournamentId, $fixture) {
        $queryResult = self::getFixture($tournamentId);
        $queryResult[FIXTURE] = json_encode($fixture, JSON_UNESCAPED_UNICODE);
        $queryResult->save();
    }

    /** -------------------- GAME QUERIES -------------------- **/

    /**
     * @description query to create new game
     * @param $parameters
     * @return mixed
     */
    public static function setGame($parameters) {
        $queryResult = Game::create([
            GAME_NAME => $parameters[GAME_NAME],
            GAME_IMAGE => $parameters[GAME_IMAGE],
            PLAYING_TYPE => json_encode($parameters[PLAYING_TYPE], JSON_UNESCAPED_UNICODE),
            PLATFORMS => json_encode($parameters[PLATFORMS], JSON_UNESCAPED_UNICODE),
            DEVELOPER => $parameters[DEVELOPER],
            GAME_TYPE => $parameters[GAME_TYPE]
        ]);

        return $queryResult;
    }

    /**
     * @description query to get games
     * @param integer $gameId
     * @return mixed
     */
    public static function getGame($gameId = null) {
        $queryResult = Game::where(function ($query) use ($gameId) {
            if ($gameId) {
                $query->where(GAME_ID, EQUAL_SIGN, $gameId);
            }
        })->get();

        return $queryResult;
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
            PAYMENT_TYPE => $parameters[PAYMENT_TYPE],
            REFERENCE_CODE => $parameters[REFERENCE_CODE]
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
            ->join(DB_USERS_TABLE, (DB_USERS_TABLE . '.' . IDENTIFIER), EQUAL_SIGN, (DB_PARTICIPANT_TABLE . '.' . PARTICIPANT_ID))
            ->where(function ($query) use ($participantId) {
                if ($participantId) {
                    $query->where(PARTICIPANT_ID, EQUAL_SIGN, $participantId);
                }
            })
            ->get();

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

    /**
     * @description query to update participant point
     * @param integer $tournamentId
     * @param integer $participantId
     * @param integer $point
     */
    public static function updateParticipantPoint($tournamentId, $participantId, $point) {
        Participant::where(TOURNAMENT_ID, EQUAL_SIGN, $tournamentId)
            ->where(PARTICIPANT_ID, EQUAL_SIGN, $participantId)
            ->update([POINT => $point]);
    }

    /**
     * @description query to update participant earnings and ranking
     * @param $participant
     */
    public static function updateParticipantRankingAndEarnings($participant) {
        Participant::where(TOURNAMENT_ID, EQUAL_SIGN, $participant[TOURNAMENT_ID])
            ->where(PARTICIPANT_ID, EQUAL_SIGN, $participant[PARTICIPANT_ID])
            ->update([
                EARNINGS => $participant[EARNINGS],
                TOURNAMENT_RANKING => $participant[TOURNAMENT_RANKING]
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
            START_DATE => strval($tournament[START_DATE]),
            STATUS => $tournament[STATUS],
            DAYS => $tournament[DAYS]
        ]);

        return $queryResult;
    }

    /**
     * @description query to check if holder have any tournament on selected date.
     * @param mixed $parameters
     * @param integer $holderId
     * @return mixed
     */
    public static function checkTournamentsDifferenceIsOneDay($parameters, $holderId) {
        $queryResult = true;
        $tournaments = Tournament::where(HOLDER_ID, EQUAL_SIGN, $holderId)
            ->where(function ($query) {
                $query->where(STATUS, EQUAL_SIGN, TOURNAMENT_STATUS_OPEN);
            })->get();

        foreach ($tournaments as $tournament) {
            if (($tournament[START_DATE] - $parameters[START_DATE]) < 86400000 && ($parameters[START_DATE] - $tournament[START_DATE]) < 86400000) {
                Log::info('holder: ' . $holderId . ' try to set tournament in same day');
                Log::info('given date is: ' . $parameters[START_DATE]);
                Log::info('exist date is: ' . $tournament[START_DATE]);
                $queryResult = false;
                return $queryResult;
            }
        }

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


    /**
     * @description query to get tournament with detail info
     * @param integer $tournamentId
     * @return mixed
     */
    public static function getTournamentWithDetail($tournamentId) {
        $queryResult = Tournament::where(TOURNAMENT_ID, EQUAL_SIGN, $tournamentId)
            ->join(DB_USERS_TABLE, (DB_USERS_TABLE . '.' . IDENTIFIER), EQUAL_SIGN, DB_TOURNAMENT_TABLE . '.' . HOLDER_ID)
            ->join(DB_GAME_TABLE, (DB_GAME_TABLE . '.' . GAME_ID), EQUAL_SIGN, DB_TOURNAMENT_TABLE . '.' . GAME_ID)
            ->first();

        return $queryResult;
    }

    /**
     * @description query to get all open status tournaments
     * @return mixed
     */
    public static function getAllOpenTournaments() {
        $queryResult = Tournament::where([
            [STATUS, EQUAL_SIGN, TOURNAMENT_STATUS_OPEN]
        ])->get();

        return $queryResult;
    }

    /**
     * @description query to get tournament general detail
     * @param $parameters
     * @param $user
     * @return mixed
     */
    public static function searchTournaments($parameters, $user) {
        $queryResult = Tournament::where(STATUS, EQUAL_SIGN, $parameters[STATUS])
            ->join(DB_USERS_TABLE, (DB_USERS_TABLE.'.'.IDENTIFIER), EQUAL_SIGN, DB_TOURNAMENT_TABLE.'.'.HOLDER_ID)
            ->join(DB_GAME_TABLE, (DB_GAME_TABLE.'.'.GAME_ID), EQUAL_SIGN, DB_TOURNAMENT_TABLE.'.'.GAME_ID)

            ->where(DB_USERS_TABLE.'.'.CITY, LIKE_SIGN, $user[CITY])
            ->get();

        return $queryResult;
    }

    /**
     * @description query to get participant`s tournament general detail
     * @param $parameters
     * @param $user
     * @return mixed
     */
    public static function searchParticipantTournaments($parameters, $user) {
        $queryResult = array();
        $userTournaments = Participant::where(DB_PARTICIPANT_TABLE.'.'.PARTICIPANT_ID, EQUAL_SIGN, $user[IDENTIFIER])->get();

        foreach ($userTournaments as $userTournament) {
            $result = Tournament::where(TOURNAMENT_ID, EQUAL_SIGN, $userTournament[TOURNAMENT_ID])
                ->where(STATUS, EQUAL_SIGN, $parameters[STATUS])
                ->join(DB_USERS_TABLE, (DB_USERS_TABLE.'.'.IDENTIFIER), EQUAL_SIGN, DB_TOURNAMENT_TABLE.'.'.HOLDER_ID)
                ->join(DB_GAME_TABLE, (DB_GAME_TABLE.'.'.GAME_ID), EQUAL_SIGN, DB_TOURNAMENT_TABLE.'.'.GAME_ID)
                ->first();
            array_push($queryResult, $result);
        }
        return $queryResult;
    }

    /**
     * @description query to update tournament`s status
     * @param $tournament
     * @param $status
     * @return mixed
     */
    public static function updateTournamentStatus($tournament, $status) {
        $tournament[STATUS] = $status;
        $tournament->save();
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
            DISTRICT => $user[DISTRICT],
            PICTURE => $user[PICTURE],
            ONESIGNAL_DEVICE_ID => $user[ONESIGNAL_DEVICE_ID]
        ]);
    }

    /**
     * @description query to update user.
     * @param $userId
     * @param $parameters
     * @return mixed
     */
    public static function updateUser($userId, $parameters) {
        $user = self::getUserById($userId);
        !empty($parameters[NAME]) && ($user[NAME] = $parameters[NAME]);
        !empty($parameters[SURNAME]) && ($user[SURNAME] = $parameters[SURNAME]);
        !empty($parameters[PHONE]) && ($user[PHONE] = $parameters[PHONE]);
        !empty($parameters[BIRTHDAY]) && ($user[BIRTHDAY] = strval($parameters[BIRTHDAY]));
        ($user[STATE] == USER_STATE_DISABLE) && ($user[STATE] = USER_STATE_ACTIVE);
        $user->save();
        return $user;
    }
}
