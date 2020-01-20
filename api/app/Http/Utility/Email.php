<?php

namespace App\Http\Utility;

use Mail;

class Email {

    public static function send($type, $data) {
        if ($type == RESET_PASSWORD) {
            self::sendResetPasswordEmail($data);
        } else if ($type == WELCOME_EMAIL) {
            self::sendWelcomeEmail($data);
        }
    }

    private static function sendResetPasswordEmail($data) {
        $user = array(
            EMAIL => $data[EMAIL],
            PASSWORD => $data[PASSWORD]
        );
        Mail::send('emails/resetPassword', $user, function ($message) use ($user) {
            $message->subject('🎉 Şifre Yenileme');
            $message->from(NO_REPLY, 'tunuvaz');
            $message->to($user[EMAIL]);
        });
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
            $message->from(NO_REPLY, 'turnuvaz');
            $message->to($user[EMAIL]);
        });
    }

}
