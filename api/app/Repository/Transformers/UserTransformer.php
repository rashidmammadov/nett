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
            TYPE => $user->type,
            IDENTIFIER => $user->id,
            USERNAME => $user->username,
            NAME => $user->name,
            SURNAME => $user->surname,
            BIRTHDAY => $user->birthdat,
            EMAIL => $user->email,
            SEX => $user->sex,
            REMEMBER_TOKEN => $user->remember_token,
            STATE => $user->state
        ];
    }
}
