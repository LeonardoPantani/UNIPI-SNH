<?php

namespace App\Controllers;

require_once __DIR__ . '/../libs/utils/validator/validator.php';
require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../libs/utils/mail/sendmail.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../libs/utils/view/ViewManager.php';

use App\Models\User;
use App\Utils\Validator;
use App\Utils\ViewManager;

class UserController {
    private array $server;
    private array $params;

    public function __construct(array $server, array $params_get, array $params_post) {
        $this->server = $server;

        $this->params = array(
            'GET'  => $params_get,
            'POST' => $params_post
        );
    }

    // GET /storyforge/registration.php
    function new() {
        $logger = getLogger('registration');
        $logger->info('GET /storyforge/registration.php');

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("registration", ["flash" => $flash, "email_pattern" => Validator::EMAIL_REGEX, "username_pattern" => Validator::USERNAME_REGEX_HTML, "username_minlength" => Validator::USERNAME_MIN_LENGTH, "username_maxlength" => Validator::USERNAME_MAX_LENGTH, "password_minlength" => Validator::PASSWORD_MIN_LENGTH]);
    }

    // POST /storyforge/registration.php
    function create() {
        $logger = getLogger('registration');
        $logger->info('POST /storyforge/registration.php');

        $email            = $this->params['POST']['email'];
        $username         = $this->params['POST']['username'];
        $password         = $this->params['POST']['password'];
        $password_confirm = $this->params['POST']['password_confirm'];

        if(!Validator::emailValidation($email)) {
            $logger->info('Invalid email');
            $_SESSION['flash']['error'] = 'Invalid email';
            $this->new();
            return;
        }

        if(!Validator::usernameValidation($username)) {
            $logger->info('Invalid username');
            $_SESSION['flash']['error'] = 'Username length must be at least '. Validator::USERNAME_MIN_LENGTH .' chars and less than '. Validator::USERNAME_MAX_LENGTH . ' and can only contain letters, numbers, dashes and underscores.';
            $this->new();
            return;
        }

        if(!Validator::passwordValidation($password)) {
            $logger->info('Invalid password');
            $_SESSION['flash']['error'] = 'The password must be at least '. Validator::PASSWORD_MIN_LENGTH .' chars long';
            $this->new();
            return;
        }

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

        sendEmail($email, "Welcome to StoryForge!", "welcome", ["username" => $username]);

        $_SESSION['flash']['success'] = 'User <strong>'.$username.'</strong> created!';
        header("Location: ". "login.php");
    }
}