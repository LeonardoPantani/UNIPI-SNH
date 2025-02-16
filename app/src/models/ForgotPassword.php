<?php

namespace App\Models;

require_once __DIR__ . '/../libs/utils/db/DBConnection.php';
require_once __DIR__ . '/../libs/utils/mail/sendmail.php';
require_once __DIR__ . '/../libs/utils/utility/utility.php';

use App\Utils\DBConnection;

class ForgotPassword extends DBConnection {
    public const string INTERVAL = "2 minutes";
    private const int RANDOM_STRING_LENGTH = 5;
    
    private int $id;
    private string $random_string;
    private string $expire_at;
    private int $user_id;

    public function getId() {
        return $this->id;
    }

    public function getRandomString() {
        return $this->random_string;
    }

    public function getExpireAt() {
        return $this->expire_at;
    }

    public function getUserId() {
        return $this->user_id;
    }

    private function __construct(int $user_id, string $random_string, ?string $expire_at) {
        $this->user_id = $user_id;
        $this->random_string = $random_string;
        $this->expire_at = $expire_at;
    }

    private static function generateRandomString() {
        return generate_random_string(self::RANDOM_STRING_LENGTH);
    }

    public static function get_user_id_by_code($random_string) : ?ForgotPassword {
        $row = self::db_fetchOne(
            "SELECT * FROM password_challenge WHERE random_string = ? AND expire_at > NOW()", 
            [$random_string]
        );

        if(count($row) <= 0) {
            return null;
        }

        return new ForgotPassword(
            (int) $row['id'],
            $row['random_string'],
            $row['expire_at'],
            (int) $row['user_id']
        );
    }

    public static function add_code($user_id) : ?ForgotPassword {
        $id = self::db_getLastInsertId(
            "INSERT INTO password_challenge (random_string, expire_at, user_id) VALUES (?, ?, ?)",
            [self::generateRandomString(), date('Y-m-d H:i:s', strtotime("+".self::INTERVAL)), $user_id]
        );

        if($id <= 0) {
            return null;
        }

        $row = self::db_fetchOne(
            "SELECT * FROM password_challenge WHERE id = ?",
            [$id]
        );

        if(count($row) <= 0) {
            return null;
        }

        return new ForgotPassword(
            (int) $row['id'],
            $row['random_string'],
            $row['expire_at'],
            (int) $row['user_id']
        );
    }

    public static function update_code($user_id) : ?ForgotPassword {
        $res = self::db_getOutcome(
            "UPDATE password_challenge SET random_string = ?, expire_at = ? WHERE user_id = ?",
            [self::generateRandomString(), date('Y-m-d H:i:s', strtotime("+".self::INTERVAL)), $user_id]
        );

        if(!$res) {
            return null;
        }

        $row = self::db_fetchOne(
            "SELECT * FROM password_challenge WHERE user_id = ?",
            [$user_id]
        );

        if(count($row) <= 0) {
            return null;
        }

        return new ForgotPassword(
            (int) $row['id'],
            $row['random_string'],
            $row['expire_at'],
            (int) $row['user_id']
        );
    }

    public static function delete_code($user_id) : bool {
        return self::db_getOutcome(
            "DELETE FROM password_challenge WHERE user_id = ?", 
            [$user_id]
        );
    }

    public static function pending_request_by_user_id($user_id) : ?ForgotPassword {
        $row = self::db_fetchOne(
            "SELECT * FROM password_challenge WHERE user_id = ?", 
            [$user_id]
        );

        if(count($row) <= 0) {
            return null;
        }

        return new ForgotPassword(
            (int) $row['id'],
            $row['random_string'],
            $row['expire_at'],
            (int) $row['user_id'],
        );
    }
}