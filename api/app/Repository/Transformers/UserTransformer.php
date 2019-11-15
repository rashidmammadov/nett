<?php
/**
 * Created by IntelliJ IDEA.
 * User: rashid
 * Date: 19.02.2019
 * Time: 11:42
 */

namespace App\Repository\Transformers;


class UserTransformer extends Transformer {

    public function transform($user) {
        return [
            TYPE => $user[TYPE],
            IDENTIFIER => $user[IDENTIFIER],
            USERNAME => $user[USERNAME],
            NAME => $user[NAME],
            PICTURE => $user[PICTURE],
            PHONE => $user[PHONE],
            SURNAME => $user[SURNAME],
            BIRTHDAY => $user[BIRTHDAY],
            EMAIL => $user[EMAIL],
            SEX => $user[SEX],
            BUDGET => number_format((float)$user[BUDGET], 2, '.', ''),
            TICKET => $user[TICKET],
            REMEMBER_TOKEN => $user[REMEMBER_TOKEN],
            CITY => $user[CITY],
            DISTRICT => $user[DISTRICT],
            RANKING => $user[RANKING],
            PREVIOUS_RANKING => $user[PREVIOUS_RANKING],
            STATE => $user[STATE]
        ];
    }
}
