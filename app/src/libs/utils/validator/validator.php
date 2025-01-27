<?php

namespace App\Utils;

class Validator {
    public const PASSWORD_MIN_LENGTH = 8;
    public const USERNAME_MIN_LENGTH = 3;
    public const USERNAME_MAX_LENGTH = 50;
    #public const USERNAME_REGEX = "/^[a-z0-9]{3,25}$/i";
    public const EMAIL_REGEX = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/";

    public static function emailValidation(string $email) : bool {
        return preg_match(self::EMAIL_REGEX, $email);
    }

    public static function passwordValidation(string $password) : bool {
        return strlen($password) >= self::PASSWORD_MIN_LENGTH;
    }

    public static function usernameValidation($username) : bool {
        return (strlen($username) >= self::USERNAME_MIN_LENGTH && strlen($username) <= self::USERNAME_MAX_LENGTH);
    }
}