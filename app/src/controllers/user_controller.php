<?php

namespace App\Controllers;

require_once __DIR__ . '/../libs/utils/validator/validator.php';
require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../libs/utils/mail/sendmail.php';
require_once __DIR__ . '/../models/User.php';

use App\Models\User;
use App\Utils\Validator;

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

        $username_pattern = Validator::USERNAME_REGEX_HTML;

        include __DIR__ . '/../views/registration_view.php';
    }

    // POST /storyforge/registration.php
    function create() {
        $flash = array();
        $logger = getLogger('registration');
        $logger->info('POST /storyforge/registration.php');

        $email            = $this->params['POST']['email'];
        $username         = $this->params['POST']['username'];
        $password         = $this->params['POST']['password'];
        $password_confirm = $this->params['POST']['password_confirm'];

        if(!Validator::emailValidation($email)) {
            $logger->info('Invalid email');
            $flash['error'] = 'Invalid email';
            include __DIR__ . '/../views/registration_view.php';

            return;
        }

        if(!Validator::usernameValidation($username)) {
            $logger->info('Invalid username');
            $flash['error'] = 'Username length must be at least '. Validator::USERNAME_MIN_LENGTH .' chars but less than '. Validator::USERNAME_MAX_LENGTH . '. Username field only accepts letters, numbers, dashes and underscores.';
            include __DIR__ . '/../views/registration_view.php';

            return;
        }

        if(!Validator::passwordValidation($password)) {
            $logger->info('Invalid password');
            $flash['error'] = 'The password must be at least '. Validator::PASSWORD_MIN_LENGTH .' chars long';
            include __DIR__ . '/../views/registration_view.php';

            return;
        }

        if($password !== $password_confirm) {
            $logger->info('Invalid confirm password');
            $flash['error'] = 'Mismatch between password and password confirm';
            include __DIR__ . '/../views/registration_view.php';

            return;
        }

        if(User::usernameExists($username)) {
            $logger->info('Username already taken');
            $flash['error'] = 'Username already taken';
            include __DIR__ . '/../views/registration_view.php';

            return;
        }

        if(User::emailExists($email)) {
            $logger->info('Email already taken');
            $flash['error'] = 'Email already taken';
            include __DIR__ . '/../views/registration_view.php';

            return;
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $res = User::addUser($email, $username, $password_hash);

        if(!$res) {
            $logger->info('Database error during user registration');
            $flash['error'] = 'Invalid user data';
            include __DIR__ . '/../views/registration_view.php';

            return;
        }

        /*
        $subject = 'Registrazione confermata'
        $body = '
            <h1>Buongiorno</h1>
            <br>

            <p>La registrazione è confermata.</p>
            <p>Saluti</p>
        '
        sendEmail($email, $subject, $body)
        */

        $_SESSION['flash']['success'] = 'User created!';
        header("Location: ". "login.php");
    }

}