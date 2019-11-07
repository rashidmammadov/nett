<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 18.04.2019
 * Time: 15:45
 */

namespace App\Http\Utility;

use App\Http\Queries\MySQL\ApiQuery;
use Illuminate\Support\Facades\Log;

class PushNotification {

    /**
     * @description: prepare message for started tournament.
     * @param $tournamentId : holds the tournament`s id
     * @param $userId : holds the user id
     * @param $amount : holds the user`s earnings amount
     * @param $ticket : holds the user`s earnings ticket
     */
    public static function payedTournamentEarnings($tournamentId, $userId, $amount, $ticket) {
        $tournament = ApiQuery::getTournament($tournamentId);
        $headings = array('en' => 'Tebrikler!');
        $startDate = CustomDate::getDateFromMilliseconds($tournament[START_DATE]);
        $data = array(TOURNAMENT_ID => $tournamentId);
        $game = ApiQuery::getGame($tournament[GAME_ID])->first();

        /** message for participants and holder **/
        $gift = null;
        if ($amount > 0) {
            $gift = $amount . ' ' . TURKISH_LIRA;
        } else if ($ticket > 0) {
            $gift = $ticket . ' ' . TICKET_TR;
        }
        if ($gift) {
            $participantContent = array('en' =>
                $startDate . ' tarihli ' . $game[GAME_NAME] . ' turnuvasından kazandığınız ' . $gift . ' hesabınıza aktarıldı'
            );
            $onesignal_ids = self::getOneSignalId($userId);
            self::send($headings, $participantContent, $onesignal_ids, $data);
        }
    }

    /**
     * @description: prepare message for started tournament.
     * @param $tournament : data of started tournament.
     * @param $participants : list of participants.
     */
    public static function tournamentStarts($tournament, $participants) {
        $headings = array('en' => 'Geri Sayım Başladı');
        $startDate = CustomDate::getDateFromMilliseconds($tournament[START_DATE]);
        $data = array(TOURNAMENT_ID => $tournament[TOURNAMENT_ID]);
        $game = ApiQuery::getGame($tournament[GAME_ID])->first();

        /** message for participants **/
        $participantContent = array('en' =>
            $startDate . ' tarihli ' . $game[GAME_NAME] . ' turnuvası 24 saat içinde başlıyor, rakiplerini ve fikstürü görmek için tıkla'
        );
        $participants_onesignal_ids = self::getParticipantsOneSignalIds($participants);

        /** message for holder **/
        $holderContent = array('en' =>
            $startDate . ' tarihli ' . $game[GAME_NAME] . ' turnuvan 24 saat içinde başlıyor, detaylar için tıkla'
        );
        $holder_onesignal_id = self::getHolderOneSignalId($tournament);

        self::send($headings, $participantContent, $participants_onesignal_ids, $data);
        self::send($headings, $holderContent, $holder_onesignal_id, $data);
    }

    /**
     * @description: prepare message for cancelled tournament.
     * @param $tournament : data of cancelled tournament.
     * @param $participants : list of participants.
     */
    public static function tournamentCancelled($tournament, $participants) {
        $headings = array('en' => 'Turnuva İptali');
        $startDate = CustomDate::getDateFromMilliseconds($tournament[START_DATE]);
        $data = array(TOURNAMENT_ID => $tournament[TOURNAMENT_ID]);
        $game = ApiQuery::getGame($tournament[GAME_ID])->first();

        /** message for participants **/
        $participantContent = array('en' =>
            $startDate . ' tarihli ' . $game[GAME_NAME] . ' turnuvası yeterli katılımcı sayısına ulaşmadığı için iptal edilmiştir, ödemen hesabına geri yatırılmıştır'
        );
        $participants_onesignal_ids = self::getParticipantsOneSignalIds($participants);

        /** message for holder **/
        $holderContent = array('en' =>
            $startDate . ' tarihli ' . $game[GAME_NAME] . ' turnuvası yeterli katılımcı sayısına ulaşmadığı için iptal edilmiştir, uygun bir tarih için yeni bir turnuva oluşturabilirsin'
        );
        $holder_onesignal_id = self::getHolderOneSignalId($tournament);

        self::send($headings, $participantContent, $participants_onesignal_ids, $data);
        self::send($headings, $holderContent, $holder_onesignal_id, $data);
    }


    /**
     * @description: send push notification message to subscribers.
     * @param $headings : message headings
     * @param $content : message content
     * @param $onesignal_ids : one signal users id
     * @param $data
     */
    private static function send($headings, $content, $onesignal_ids, $data) {
        $fields = array(
            'app_id' => env('ONESIGNAL_APP_ID'),
            'include_player_ids' => $onesignal_ids,
            'small_icon' => 'ic_stat_onesignal_default',
            'headings' => $headings,
            'contents' => $content,
            'data' => $data
        );

        $fields = json_encode($fields);
        Log::info($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        self::response($response);
    }

    private static function response($response) {
        $return["allresponses"] = $response;
        $return = json_encode($return);

        Log::info($return);
    }

    private static function getOneSignalId($userId) {
        $oneSignalId = array();
        $user = ApiQuery::getUserById($userId);
        array_push($oneSignalId, $user[ONESIGNAL_DEVICE_ID]);
        return $oneSignalId;
    }

    private static function getParticipantsOneSignalIds($participants) {
        $participants_onesignal_ids = array();
        foreach ($participants as $participant) {
            array_push($participants_onesignal_ids, $participant[ONESIGNAL_DEVICE_ID]);
        }
        return $participants_onesignal_ids;
    }

    private static function getHolderOneSignalId($tournament) {
        $holder = ApiQuery::getUserById($tournament[HOLDER_ID]);
        $holder_onesignal_id = array($holder[ONESIGNAL_DEVICE_ID]);
        return $holder_onesignal_id;
    }

}
