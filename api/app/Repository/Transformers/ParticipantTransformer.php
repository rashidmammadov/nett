<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 03.04.2019
 * Time: 10:13
 */

namespace App\Repository\Transformers;


class ParticipantTransformer extends Transformer
{

    public function transform($participant)
    {
        return [
            PARTICIPANT_ID => $participant[PARTICIPANT_ID],
            USERNAME => $participant[USERNAME],
            NAME => $participant[NAME],
            SURNAME => $participant[SURNAME],
            PICTURE => $participant[PICTURE],
            TOURNAMENT_RANKING => $participant[TOURNAMENT_RANKING]
        ];
    }

    public function transformForHolder($participant)
    {
        return [
            PARTICIPANT_ID => $participant[PARTICIPANT_ID],
            USERNAME => $participant[USERNAME],
            NAME => $participant[NAME],
            SURNAME => $participant[SURNAME],
            PICTURE => $participant[PICTURE],
            TOURNAMENT_RANKING => $participant[TOURNAMENT_RANKING],
            PAYMENT_TYPE => $participant[PAYMENT_TYPE],
            PAYMENT_AMOUNT => $participant[PAYMENT_AMOUNT],
            REFERENCE_CODE => $participant[REFERENCE_CODE]
        ];
    }

    public function transformForFixture($participant)
    {
        return [
            PARTICIPANT_ID => $participant[PARTICIPANT_ID],
            USERNAME => $participant[USERNAME],
            POINT => $participant[POINT]
        ];
    }
}
