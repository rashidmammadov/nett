<?php

namespace App\Http\Utility;

use Mail;

class Email {

    public static function send($type, $data) {
        if ($type == WELCOME_EMAIL) {
            self::sendWelcomeEmail($data);
        }
    }

    private static function sendWelcomeEmail($data) {
        $user = array(
            TYPE => $data[TYPE],
            NAME => $data[NAME],
            SURNAME => $data[SURNAME],
            EMAIL => $data[EMAIL],
            PASSWORD => $data[PASSWORD]
        );
        Mail::send('emails/welcome', $user, function ($message) use ($user) {
            $message->subject('🎉 Hoş Geldin!');
            $message->from(NO_REPLY, 'özelden team');
            $message->to($user[EMAIL]);
        });
    }

}
