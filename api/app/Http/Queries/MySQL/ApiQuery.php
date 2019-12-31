<?php

namespace App\Http\Queries\MySQL;

use App\Finance;
use App\Fixture;
use App\Game;
use App\Match;
use App\Merchant;
use App\Participant;
use App\Tournament;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ApiQuery {

    /** -------------------- FINANCE QUERIES -------------------- **/

    /**
     * Query to get finance data with pagination.
     * @param integer $userId - the id of user.
     * @param integer $itemPerPage - the count of item per page.
     * @return array
     */
    public static function getFinanceWithPagination($userId, $itemPerPage) {
        try {
            $queryResult = Finance::where(USER_ID, EQUAL_SIGN, $userId)
                ->where(AMOUNT, NOT_EQUAL_SIGN, 0)
                ->orderBy('updated_at', 'desc')
                ->paginate($itemPerPage);
            return $queryResult;
        } catch (QueryException $e) {
            self::logException($e, debug_backtrace());
        }
    }

    /**
     * @description query to get finance dat a
     * @param integer $status - the status of finance
     * @return mixed
     */
    public static function getFinanceWithStatus($status) {
        $queryResult = Finance::where(STATUS, EQUAL_SIGN, $status)->get();
        return $queryResult;
    }

    /**
     * @description query to create new finance data
     * @param $finance
     * @return mixed
     */
    public static function setFinance($finance) {
        try {
            $queryResult = Finance::create($finance);
            return $queryResult;
        } catch (QueryException $e) {
            self::logException($e, debug_backtrace());
        }
    }

    /**
     * @description query to update finance status
     * @param $financeId - the id of finance
     * @param $status - the status is changed
     */
    public static function updateFinanceStatus($financeId, $status) {
        Finance::where(FINANCE_ID, EQUAL_SIGN, $financeId)
            ->update([STATUS => $status]);
    }

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
            ->orderBy(TOURNAMENT_RANKING)
            ->get();
        return $queryResult;
    }

    /**
     * @description query to get all participants which ranking is null
     *      and list by point.
     * @param integer $tournamentId
     * @return mixed $queryResult
     */
    public static function getParticipantsListedWithoutRanking($tournamentId) {
        $queryResult = Participant::where(TOURNAMENT_ID, EQUAL_SIGN, $tournamentId)
            ->where(TOURNAMENT_RANKING, EQUAL_SIGN, null)
            ->join(DB_USERS_TABLE, (DB_USERS_TABLE . '.' . IDENTIFIER), EQUAL_SIGN, (DB_PARTICIPANT_TABLE . '.' . PARTICIPANT_ID))
            ->orderBy(POINT, 'desc')
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
     * @description query to update participant ranking
     * @param integer $tournamentId - the played tournament`s id
     * @param integer $participantId - the participant id
     * @param integer $ranking - the participant`s ranking
     */
    public static function updateParticipantRanking($tournamentId, $participantId, $ranking) {
        Participant::where(TOURNAMENT_ID, EQUAL_SIGN, $tournamentId)
            ->where(PARTICIPANT_ID, EQUAL_SIGN, $participantId)
            ->update([TOURNAMENT_RANKING => $ranking]);
    }

    /**
     * @description query to update participant earnings
     * @param integer $tournamentId
     * @param integer $participantId
     * @param double $earnings
     */
    public static function updateParticipantEarnings($tournamentId, $participantId, $earnings) {
        Participant::where(TOURNAMENT_ID, EQUAL_SIGN, $tournamentId)
            ->where(PARTICIPANT_ID, EQUAL_SIGN, $participantId)
            ->update([EARNINGS => $earnings]);
    }

    /** -------------------- MATCH QUERIES -------------------- **/

    /**
     * @description query to set match.
     * @param integer $tournamentId
     * @param array $parameters
     */
    public static function setMatch($tournamentId, $parameters) {
        Match::create([
            TOURNAMENT_ID => $tournamentId,
            TOUR_ID => $parameters[TOUR_ID],
            TOUR_NAME => $parameters[TOUR_NAME],
            HOME_ID => $parameters[HOME_ID],
            AWAY_ID => $parameters[AWAY_ID],
            AVAILABLE => $parameters[AVAILABLE],
            DATE => $parameters[DATE]
        ]);
    }

    /**
     * @description query to get tournament matches.
     * @param integer $tournamentId
     * @return mixed
     */
    public static function getMatches($tournamentId) {
        $queryResult =  Match::where(DB_MATCH_TABLE.'.'.TOURNAMENT_ID, EQUAL_SIGN, $tournamentId)
            ->get()
            ->groupBy(TOUR_ID);
        return $queryResult;
    }

    /**
     * @description query to set match winner and loser.
     * @param integer $matchId
     * @param integer $winnerId
     * @param integer $loserId
     * @param integer $homePoint
     * @param integer $awayPoint
     * @param string $note
     */
    public static function setMatchWinner($matchId, $winnerId, $loserId, $homePoint, $awayPoint, $note) {
        Match::where(DB_MATCH_TABLE.'.'.MATCH_ID, EQUAL_SIGN, $matchId)
            ->update([
                WINNER_ID => $winnerId,
                LOSER_ID => $loserId,
                HOME_POINT => $homePoint,
                AWAY_POINT => $awayPoint,
                NOTE => $note ? $note : null,
                AVAILABLE => false
            ]);
    }

    /**
     * @description query to get tournament match.
     * @param integer $tournamentId
     * @param integer $tourId
     * @param integer $matchId
     * @return mixed
     */
    public static function getMatch($tournamentId, $tourId, $matchId) {
        $queryResult =  Match::where(DB_MATCH_TABLE.'.'.TOURNAMENT_ID, EQUAL_SIGN, $tournamentId)
            ->where(DB_MATCH_TABLE.'.'.TOUR_ID, EQUAL_SIGN, $tourId)
            ->where(DB_MATCH_TABLE.'.'.MATCH_ID, EQUAL_SIGN, $matchId)
            ->first();
        return $queryResult;
    }

    /**
     * @description query to get next round match.
     * @param integer $tournamentId
     * @param integer $tourId
     * @return mixed
     */
    public static function getNextMatch($tournamentId, $tourId) {
        $queryResult =  Match::where(DB_MATCH_TABLE.'.'.TOURNAMENT_ID, EQUAL_SIGN, $tournamentId)
            ->where(DB_MATCH_TABLE.'.'.TOUR_ID, EQUAL_SIGN, $tourId)
            ->where(DB_MATCH_TABLE.'.'.HOME_ID, NOT_EQUAL_SIGN, null)
            ->where(DB_MATCH_TABLE.'.'.AWAY_ID, EQUAL_SIGN, null)
            ->first();
        return $queryResult;
    }

    public static function updateMatchAway($matchId, $awayId) {
        Match::where(DB_MATCH_TABLE.'.'.MATCH_ID, EQUAL_SIGN, $matchId)
            ->update([
                AWAY_ID => $awayId,
                AVAILABLE => true
            ]);
    }

    public static function updateMatchHome($tournamentId, $tourId, $tourName, $date, $homeId) {
        Match::create([
            TOURNAMENT_ID => $tournamentId,
            TOUR_ID => $tourId,
            TOUR_NAME => $tourName,
            HOME_ID => $homeId,
            AVAILABLE => true,
            DATE => $date
        ]);
    }

    public static function isAllMatchPlayed($tournamentId) {
        $queryResult =  Match::where(TOURNAMENT_ID, EQUAL_SIGN, $tournamentId)
            ->where(AVAILABLE, EQUAL_SIGN, true)
            ->doesntExist();
        return $queryResult;
    }

    /** -------------------- MERCHANT QUERIES -------------------- **/

    /**
     * @description query to set merchant data.
     * @param integer $userId - the id user
     * @param $merchant - the data of merchant
     * @return mixed
     */
    public static function setMerchant($userId, $merchant) {
        try {
            $queryResult = Merchant::create([
                MERCHANT_ID => $userId,
                MERCHANT_TYPE => $merchant[MERCHANT_TYPE],
                MERCHANT_KEY => $merchant[MERCHANT_KEY],
                IDENTITY_NUMBER => $merchant[IDENTITY_NUMBER],
                TAX_OFFICE => $merchant[TAX_OFFICE],
                TAX_NUMBER => $merchant[TAX_NUMBER],
                COMPANY_TITLE => $merchant[COMPANY_TITLE],
                IBAN => $merchant[IBAN]
            ]);
            return $queryResult;
        } catch (QueryException $e) {
            self::logException($e, debug_backtrace());
        }
    }

    /**
     * Query to update merchant key.
     * @param integer $merchantId - id of merchant.
     * @param string $merchantKey - key of merchant from iyzico.
     * @return mixed
     */
    public static function setMerchantKey($merchantId, $merchantKey) {
        try {
            $queryResult = Merchant::where(MERCHANT_ID, EQUAL_SIGN, $merchantId)
                ->update([MERCHANT_KEY => $merchantKey]);
            return $queryResult;
        } catch (QueryException $e) {
            self::logException($e, debug_backtrace());
        }
    }

    /**
     * @description query to get merchant data.
     * @param integer $userId - the id user
     * @return mixed
     */
    public static function getMerchant($userId) {
        try {
            $queryResult = Merchant::where(MERCHANT_ID, EQUAL_SIGN, $userId)
                ->first();
            return $queryResult;
        } catch (QueryException $e) {
            self::logException($e, debug_backtrace());
        }
    }

    /**
     * @description query to update merchant data.
     * @param integer $merchantId - holds the merchant id.
     * @param $parameters - holds the merchant data.
     * @return mixed
     */
    public static function updateMerchant($merchantId, $parameters) {
        try {
            $merchant = self::getMerchant($merchantId);
            !empty($parameters[MERCHANT_TYPE])      && ($merchant[MERCHANT_TYPE] = $parameters[MERCHANT_TYPE]);
            !empty($parameters[IDENTITY_NUMBER])    && ($merchant[IDENTITY_NUMBER] = $parameters[IDENTITY_NUMBER]);
            !empty($parameters[TAX_OFFICE])         && ($merchant[TAX_OFFICE] = $parameters[TAX_OFFICE]);
            !empty($parameters[TAX_NUMBER])         && ($merchant[TAX_NUMBER] = $parameters[TAX_NUMBER]);
            !empty($parameters[COMPANY_TITLE])      && ($merchant[COMPANY_TITLE] = $parameters[COMPANY_TITLE]);
            !empty($parameters[IBAN])               && ($merchant[IBAN] = $parameters[IBAN]);
            $merchant->save();
            return $merchant;
        } catch (QueryException $e) {
            self::logException($e, debug_backtrace());
        }
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
            PARTICIPATION_FEE => $tournament[PARTICIPATION_FEE],
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
     * @description query to get holder`s tournament general detail
     * @param $parameters
     * @param $user
     * @return mixed
     */
    public static function searchHolderTournaments($parameters, $user) {
        $queryResult = array();
        $holderTournaments = Tournament::where(HOLDER_ID, EQUAL_SIGN, $user[IDENTIFIER])->get();

        foreach ($holderTournaments as $holderTournament) {
            $result = Tournament::where(TOURNAMENT_ID, EQUAL_SIGN, $holderTournament[TOURNAMENT_ID])
                ->where(STATUS, EQUAL_SIGN, $parameters[STATUS])
                ->join(DB_USERS_TABLE, (DB_USERS_TABLE.'.'.IDENTIFIER), EQUAL_SIGN, DB_TOURNAMENT_TABLE.'.'.HOLDER_ID)
                ->join(DB_GAME_TABLE, (DB_GAME_TABLE.'.'.GAME_ID), EQUAL_SIGN, DB_TOURNAMENT_TABLE.'.'.GAME_ID)
                ->first();
            array_push($queryResult, $result);
        }
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
     * Query to create new user.
     * @param mixed $user
     * @return mixed
     */
    public static function setUser($user) {
        try {
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
            return true;
        } catch (QueryException $e) {
            self::logException($e, debug_backtrace());
        }
    }

    /**
     * @description query to activate user.
     * @param $userId
     * @param $parameters
     * @return mixed
     */
    public static function activateUser($userId, $parameters) {
        $user = self::getUserById($userId);
        !empty($parameters[NAME])               && ($user[NAME] = $parameters[NAME]);
        !empty($parameters[SURNAME])            && ($user[SURNAME] = $parameters[SURNAME]);
        !empty($parameters[PHONE])              && ($user[PHONE] = $parameters[PHONE]);
        !empty($parameters[BIRTHDAY])           && ($user[BIRTHDAY] = strval($parameters[BIRTHDAY]));
        !empty($parameters[ADDRESS])            && ($user[ADDRESS] = strval($parameters[ADDRESS]));
        ($user[STATE] == USER_STATE_DISABLE)    && ($user[STATE] = USER_STATE_ACTIVE);
        $user->save();
        return $user;
    }

    /**
     * @description query to update user.
     * @param $userId
     * @param $parameters
     * @return mixed
     */
    public static function updateUserSettings($userId, $parameters) {
        $user = self::getUserById($userId);
        !empty($parameters[NAME])       && ($user[NAME] = $parameters[NAME]);
        !empty($parameters[SURNAME])    && ($user[SURNAME] = $parameters[SURNAME]);
        !empty($parameters[CITY])       && ($user[CITY] = $parameters[CITY]);
        !empty($parameters[DISTRICT])   && ($user[DISTRICT] = $parameters[DISTRICT]);
        !empty($parameters[PHONE])      && ($user[PHONE] = $parameters[PHONE]);
        !empty($parameters[ADDRESS])    && ($user[ADDRESS] = $parameters[ADDRESS]);
        $user->save();
        return $user;
    }

    /**
     * Query to update user` budget.
     * @param integer $userId - the given user`s id
     * @param double $budget - updated budget amount
     * @return mixed
     */
    public static function updateUserBudget($userId, $budget) {
        try {
            $queryResult = User::where(IDENTIFIER, EQUAL_SIGN, $userId)
                ->update([BUDGET => $budget]);
            return $queryResult;
        } catch (QueryException $e) {
            self::logException($e, debug_backtrace());
        }
    }

    /**
     * @description query to update user` ticket.
     * @param integer $userId - the given user`s id
     * @param double $ticket - updated ticket count
     */
    public static function updateUserTicket($userId, $ticket) {
        User::where(IDENTIFIER, EQUAL_SIGN, $userId)
            ->update([TICKET => $ticket]);
    }

    /**
     * @description query to update user` password.
     * @param integer $email - the given user`s email
     * @param string $newPassword - updated password
     */
    public static function updateUserPassword($email, $newPassword) {
        User::where(EMAIL, EQUAL_SIGN, $email)
            ->update([PASSWORD => \Hash::make($newPassword)]);
    }

    /**
     * @description query to update user`s previous and current rankings.
     * @param integer $userId
     * @param integer $ranking
     * @param integer $previousRanking
     */
    public static function updateUserRanking($userId, $ranking, $previousRanking) {
        User::where(IDENTIFIER, EQUAL_SIGN, $userId)
            ->update([RANKING => $ranking, PREVIOUS_RANKING => $previousRanking]);
    }

    /**
     * @description query to get user which are participated to closed tournaments.
     * @return mixed
     */
    public static function getPlayersListByTournamentPoint() {
        $result = User::where(DB_USERS_TABLE.'.'.TYPE, EQUAL_SIGN, PLAYER)
            ->where(DB_USERS_TABLE.'.'.STATE, EQUAL_SIGN, USER_STATE_ACTIVE)
            ->join(DB_PARTICIPANT_TABLE, DB_PARTICIPANT_TABLE.'.'.PARTICIPANT_ID, EQUAL_SIGN, DB_USERS_TABLE.'.'.IDENTIFIER)
            ->where(DB_PARTICIPANT_TABLE.'.'.TOURNAMENT_RANKING, NOT_EQUAL_SIGN, null)
            ->where(DB_PARTICIPANT_TABLE.'.'.POINT, NOT_EQUAL_SIGN, null)
            ->get()
            ->groupBy(IDENTIFIER);
        return $result;
    }

    /** -------------------- REPORT QUERIES -------------------- **/

    /**
     * Query to get holder`s earning report result.
     * @param integer $userId - the given user`s id
     * @return mixed
     */
    public static function getEarningReport($userId) {
        $result = Finance::where(DB_FINANCE_TABLE.'.'.USER_ID, EQUAL_SIGN, $userId)
            ->where(DB_FINANCE_TABLE.'.'.CHANNEL, EQUAL_SIGN, TOURNAMENT)
            ->where(DB_FINANCE_TABLE.'.'.STATUS, EQUAL_SIGN, FINANCE_STATUS_APPROVED)
            ->join(DB_TOURNAMENT_TABLE, DB_TOURNAMENT_TABLE.'.'.TOURNAMENT_ID, EQUAL_SIGN, DB_FINANCE_TABLE.'.'.TOURNAMENT_ID)
            ->join(DB_GAME_TABLE, DB_GAME_TABLE.'.'.GAME_ID, EQUAL_SIGN, DB_TOURNAMENT_TABLE.'.'.GAME_ID)
            ->orderBy(START_DATE, 'desc')
            ->offset(0)
            ->limit(10)
            ->get();
        return $result;
    }

    /**
     * Query to get participant`s finance report result.
     * @param integer $userId - the given user`s id
     * @return mixed
     */
    public static function getFinanceReport($userId) {
        $result = Finance::where(DB_FINANCE_TABLE.'.'.USER_ID, EQUAL_SIGN, $userId)
//            ->where(DB_FINANCE_TABLE.'.'.TYPE, EQUAL_SIGN, PLAYER)
            ->where(DB_FINANCE_TABLE.'.'.STATUS, EQUAL_SIGN, FINANCE_STATUS_APPROVED)
            ->get()
            ->groupBy(CHANNEL);
        return $result;
    }

    /**
     * Query to get holder`s notification report result.
     * @param integer $userId - the given user`s id
     * @param integer $limit - the limit of query
     * @return mixed
     */
    public static function getHolderNotificationReport($userId, $limit) {
        $result = Tournament::where(DB_TOURNAMENT_TABLE.'.'.HOLDER_ID, EQUAL_SIGN, $userId)
            ->join(DB_GAME_TABLE, DB_GAME_TABLE.'.'.GAME_ID, EQUAL_SIGN, DB_TOURNAMENT_TABLE.'.'.GAME_ID)
            ->orderBy(START_DATE, 'desc')
            ->offset(0)
            ->limit($limit)
            ->get();
        return $result;
    }

    /**
     * Query to get participant`s notification report result.
     * @param integer $userId - the given user`s id
     * @param integer $limit - the limit of query
     * @return mixed
     */
    public static function getPlayerNotificationReport($userId, $limit) {
        $result = Participant::where(DB_PARTICIPANT_TABLE.'.'.PARTICIPANT_ID, EQUAL_SIGN, $userId)
            ->join(DB_TOURNAMENT_TABLE, DB_TOURNAMENT_TABLE.'.'.TOURNAMENT_ID, EQUAL_SIGN, DB_PARTICIPANT_TABLE.'.'.TOURNAMENT_ID)
            ->where(DB_TOURNAMENT_TABLE.'.'.STATUS, NOT_EQUAL_SIGN, TOURNAMENT_STATUS_CANCEL)
            ->join(DB_GAME_TABLE, DB_GAME_TABLE.'.'.GAME_ID, EQUAL_SIGN, DB_TOURNAMENT_TABLE.'.'.GAME_ID)
            ->orderBy(START_DATE, 'desc')
            ->offset(0)
            ->limit($limit)
            ->get();
        return $result;
    }

    /**
     * Query to get most played games report result.
     * @return mixed
     */
    public static function getMostPlayedReport() {
        $result = Tournament::where(DB_TOURNAMENT_TABLE.'.'.GAME_ID, NOT_EQUAL_SIGN, null)
            ->join(DB_GAME_TABLE, DB_GAME_TABLE.'.'.GAME_ID, EQUAL_SIGN, DB_TOURNAMENT_TABLE.'.'.GAME_ID)
            ->get()
            ->groupBy([GAME_ID, STATUS]);
        return $result;
    }

    /**
     * Query to get player`s ranking report result.
     * @param $limit - The limit of query.
     * @return mixed
     */
    public static function getRankingReport($limit) {
        $result = User::where(DB_USERS_TABLE.'.'.TYPE, EQUAL_SIGN, PLAYER)
            ->where(DB_USERS_TABLE.'.'.STATE, EQUAL_SIGN, USER_STATE_ACTIVE)
            ->where(DB_USERS_TABLE.'.'.RANKING, NOT_EQUAL_SIGN, null)
            ->orderBy(RANKING)
            ->offset(0)
            ->limit($limit)
            ->get();
        return $result;
    }

    /**
     * Query to get participant`s timeline report result.
     * @param integer $userId - the given user`s id
     * @return mixed
     */
    public static function getTimelineReport($userId) {
        $result = Participant::where(DB_PARTICIPANT_TABLE.'.'.PARTICIPANT_ID, EQUAL_SIGN, $userId)
            ->join(DB_TOURNAMENT_TABLE, (DB_TOURNAMENT_TABLE.'.'.TOURNAMENT_ID), EQUAL_SIGN, DB_PARTICIPANT_TABLE.'.'.TOURNAMENT_ID)
            ->where(DB_TOURNAMENT_TABLE.'.'.STATUS, EQUAL_SIGN, TOURNAMENT_STATUS_CLOSE)
            ->join(DB_GAME_TABLE, (DB_GAME_TABLE.'.'.GAME_ID), EQUAL_SIGN, DB_TOURNAMENT_TABLE.'.'.GAME_ID)
            ->orderBy(START_DATE, 'desc')
            ->get();
        return $result;
    }

    /**
     * Log exception error.
     * @param QueryException $exception - holds the exception.
     * @param array $backtrace - holds the backtrace array.
     */
    private static function logException(QueryException $exception, $backtrace) {
        $caller = array_shift($backtrace);
        Log::error($exception->getMessage() . ' (' . $caller['class'] . '->' . $caller['function'] . ':' . $caller['line'] . ')');
    }

}
