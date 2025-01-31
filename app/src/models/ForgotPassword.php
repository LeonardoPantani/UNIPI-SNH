<?php

namespace App\Models;

require_once __DIR__ . '/../libs/utils/db/DBConnection.php';
require_once __DIR__ . '/../libs/utils/mail/sendmail.php';

use App\Utils\DBConnection;

class ForgotPassword extends DBConnection {
    private int $user_id;
    private ?string $random_string;
    private ?string $expire_at;

    private static function generate_random_string($length) {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }

    public static function send_mail($email) : int {
        // verifying that email exists and obtaining id and username
        $result = self::db_fetchOne("SELECT id, username FROM users WHERE email = ?", $email);
        if(empty($result)) {
            return 0;
        }
        $username = $result['username'];
        $user_id = $result['id'];

        $need_update = false;
        // want to see if user has already a pending reset request
        $pendingRequest = self::db_fetchOne("SELECT expire_at FROM password_challenge WHERE user_id = ?", $user_id);
        if(!empty($checkDuplicate)) {
            if($pendingRequest["expire_at"] > strtotime("+5 minutes")) { // there is a expired pending request, I update its row with the new one
                $need_update = true;
            } else { // the request is not yet expired
                return 2;
            }
        }
        $random_string = self::generate_random_string(5);

        $is_sent = sendEmail($email, "Password reset request", "<h1>Hello ".$username."!</h1><p>To reset your password, please insert the following text on <a href='https://localhost:8080/storyforge/create_password.php'>this page</a>: ".$random_string.".<br><br>This password reset request expires in 5 minutes.</p>", $username);
        if($is_sent !== true) {
            return 1;
        }

        if($need_update)
            self::db_getOutcome("INSERT INTO password_challenge (user_id, random_string, expire_at) VALUES (?, ?, ?)",
                $user_id, $random_string, date('Y-m-d H:i:s', time() + 600));
        else
            self::db_getOutcome("UPDATE password_challenge SET random_string = ?, expire_at = ? WHERE user_id = ?",
                $random_string, date('Y-m-d H:i:s', time() + 600), $user_id);
        return 0;
    }


    private function __construct(?int $user_id) {
        $this->user_id = $user_id;
        $this->random_string = self::generate_random_string(5);
        $this->expire_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));
    }
}