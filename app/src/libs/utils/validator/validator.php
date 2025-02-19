<?php

namespace App\Utils;

class Validator {
    public const int PASSWORD_MIN_LENGTH    = 8;
    public const int USERNAME_MIN_LENGTH    = 5;
    public const int USERNAME_MAX_LENGTH    = 20;
    public const int NOVEL_TITLE_MAX_LENGTH = 100;
    public const int NOVEL_TEXT_MAX_LENGTH  = 500;
    public const string USERNAME_REGEX_HTML = "[a-zA-Z0-9\-_]{".self::USERNAME_MIN_LENGTH.",".self::USERNAME_MAX_LENGTH."}"; # all letters, digits and these characters: -_
    public const string USERNAME_REGEX = "/^".self::USERNAME_REGEX_HTML."$/";
    public const string PARTIAL_USERNAME_REGEX = "/^[a-zA-Z0-9\-_]{1,".self::USERNAME_MAX_LENGTH."}$/";
    public const string EMAIL_REGEX = "/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+(?:\.[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?$/";
    public const string PASSWORD_REGEX_HTML = "(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[\W]).{".self::PASSWORD_MIN_LENGTH.",}";
    public const string PASSWORD_REGEX = "/^".self::PASSWORD_REGEX_HTML."$/";
    public const string UUID_REGEX = "/^[0-9a-z]{8}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{12}$/";
    public const string CODE_REGEX = "/^[0-9a-zA-Z]{5}$/";
    public const string PARTIAL_CODE_REGEX = "/^[0-9a-zA-Z]{0,5}$/";

    public static function emailValidation(string $email) : bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function passwordValidation(string $password) : bool {
        return preg_match(self::PASSWORD_REGEX, $password);
    }

    public static function usernameValidation($username) : bool {
        return preg_match(self::USERNAME_REGEX, $username);
    }

    public static function partialUsernameValidation($partial_username) : bool {
        return preg_match(self::PARTIAL_USERNAME_REGEX, $partial_username);
    }

    public static function uuidValidation($uuid) : bool {
        return preg_match(self::UUID_REGEX, $uuid);
    }

    public static function codeValidation($code) : bool {
        return preg_match(self::CODE_REGEX, $code);
    }

    public static function codePartialValidation($code) : bool {
        return preg_match(self::PARTIAL_CODE_REGEX, $code);
    }
}