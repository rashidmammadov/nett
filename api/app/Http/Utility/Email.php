<?php

namespace App\Http\Utility;

use Illuminate\Support\Facades\Log;
use Mail;

class Email {

    public static function send($type, $data) {
        if ($type == RESET_PASSWORD) {
            return self::sendResetPasswordEmail($data);
        } else if ($type == WELCOME_EMAIL) {
            return self::sendWelcomeEmail($data);
        }
    }

    private static function sendResetPasswordEmail($data) {
        $user = array(
            EMAIL => $data[EMAIL],
            PASSWORD => $data[PASSWORD]
        );
        try {
            Mail::send('emails/resetPassword', $user, function ($message) use ($user) {
                $message->subject('🎉 Şifre Yenileme');
                $message->from(NO_REPLY, 'tunuvaz');
                $message->to($user[EMAIL]);
            });
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
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
        try {
            Mail::send('emails/welcome', $user, function ($message) use ($user) {
                $message->subject('🎉 Hoş Geldin!');
                $message->from(NO_REPLY, 'turnuvaz');
                $message->to($user[EMAIL]);
            });
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

}
