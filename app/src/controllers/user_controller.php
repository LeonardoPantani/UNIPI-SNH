<?php

namespace App\Controllers;

require_once __DIR__ . '/../libs/utils/validator/validator.php';
require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../libs/utils/mail/sendmail.php';
require_once __DIR__ . '/../libs/utils/config/constants.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../libs/utils/view/ViewManager.php';

use App\Models\User;
use App\Utils\Validator;
use App\Utils\ViewManager;

class UserController {
    // GET /registration
    public function new(): void
    {
        $logger = getLogger('registration');
        $logger->info('GET /registration');

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to access registration page but is already authenticated");
            $_SESSION['flash']['error'] = 'You are already authenticated.';
            header('Location: ' . ROOT_PATH);
            return;
        }

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("registration", ["flash" => $flash, "email_pattern" => Validator::EMAIL_REGEX, "username_pattern" => Validator::USERNAME_REGEX_HTML, "username_minlength" => Validator::USERNAME_MIN_LENGTH, "username_maxlength" => Validator::USERNAME_MAX_LENGTH, "password_minlength" => Validator::PASSWORD_MIN_LENGTH]);
    }

    // POST /registration
    public function create(array $params_post): void
    {
        $logger = getLogger('registration');
        $logger->info('POST /registration');

        if(!isset($params_post['email']) || !Validator::emailValidation($params_post['email'])) {
            $logger->info('Invalid email');
            $_SESSION['flash']['error'] = 'Invalid email';
            $this->new();
            return;
        }

        if(!isset($params_post['username']) || !Validator::usernameValidation($params_post['username'])) {
            $logger->info('Invalid username');
            $_SESSION['flash']['error'] = 'Username length must be at least '. Validator::USERNAME_MIN_LENGTH .' chars and less than '. Validator::USERNAME_MAX_LENGTH . ' and can only contain letters, numbers, dashes and underscores.';
            $this->new();
            return;
        }

        if(!isset($params_post['password']) || !Validator::passwordValidation($params_post['password'])) {
            $logger->info('Invalid password');
            $_SESSION['flash']['error'] = 'The password must be at least '. Validator::PASSWORD_MIN_LENGTH .' chars long';
            $this->new();
            return;
        }

        if(!isset($params_post['password_confirm']) || !Validator::passwordValidation($params_post['password_confirm'])) {
            $logger->info('Invalid confirmation password');
            $_SESSION['flash']['error'] = 'The password must be at least '. Validator::PASSWORD_MIN_LENGTH .' chars long';
            $this->new();
            return;
        }

        $email            = $params_post['email'];
        $username         = $params_post['username'];
        $password         = $params_post['password'];
        $password_confirm = $params_post['password_confirm'];

        if($password !== $password_confirm) {
            $logger->info('Invalid confirm password');
            $_SESSION['flash']['error'] = 'Mismatch between password and password confirm';
            $this->new();
            return;
        }

        if(User::usernameExists($username)) {
            $logger->info('Username already taken');
            $_SESSION['flash']['error'] = 'Username already taken';
            $this->new();
            return;
        }

        if(User::emailExists($email)) {
            $logger->info('Email already taken');
            $_SESSION['flash']['error'] = 'Email already taken';
            $this->new();
            return;
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $res = User::addUser($email, $username, $password_hash);

        if(!$res) {
            $logger->info('Database error during user registration');
            $_SESSION['flash']['error'] = 'Invalid user data';
            $this->new();
            return;
        }

        $is_sent = sendEmail($email, "Welcome to StoryForge!", "welcome", ["username" => $username]);
        if($is_sent !== true) {
            $logger->info('Unable to send email welcome notification.');
        }

        $_SESSION['flash']['success'] = 'User '.$username.' created!';
        header('Location: ' . LOGIN_PATH);
    }
}