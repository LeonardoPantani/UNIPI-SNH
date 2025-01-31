<?php

namespace App\Controllers;

require_once __DIR__ . '/../libs/utils/validator/validator.php';
require_once __DIR__ . '/../libs/utils/db/DBConnection.php';
require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../models/ForgotPassword.php';
require_once __DIR__ . '/../libs/utils/view/ViewManager.php';

use App\Models\ForgotPassword;
use App\Utils\ViewManager;
use App\Utils\Validator;

class ForgotPasswordController {
    private array $server;
    private array $params;

    public function __construct(array $server, array $params_get, array $params_post) {
        $this->server = $server;

        $this->params = array(
            'GET'  => $params_get,
            'POST' => $params_post
        );
    }

    // GET /storyforge/forgot_password.php
    function new() {
        $logger = getLogger('forgot password');
        $logger->info('GET /storyforge/forgot_password.php');

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to access the password reset page but is already authenticated");
            $_SESSION['flash']['error'] = 'You are already authenticated.';
            header("Location: ". "login.php");
            
            return;
        }

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("forgot_password", ["flash" => $flash, "email_pattern" => Validator::EMAIL_REGEX]);
    }

    // POST /storyforge/forgot_password.php
    function validate_reset_request() {
        $logger = getLogger('validate reset request');
        $logger->info('POST /storyforge/forgot_password.php');

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to reset its password but is already authenticated");
            $_SESSION['flash']['error'] = 'You are already authenticated.';
            header("Location: ". "login.php");

            return;
        }

        $email = $this->params['POST']['email'];
        if(!isset($email)) {
            $logger->info("User tried to reset their password without setting their email");
            $_SESSION['flash']['error'] = 'Insert your email.';
            $this->new();

            return;
        }

        $requestStatus = ForgotPassword::send_mail($email);

        switch($requestStatus) {
            case 0: // ok
                $_SESSION['flash']['success'] = 'If there is an account with that email address, you will receive an email with further instructions on how to reset your password.';
            break;

            case 1: // cannot send email
                $_SESSION['flash']['error'] = 'Ooops! Something went wrong while sending the password recovery email. Please try again later or contact support if the problem persists.';
            break;

            case 2: // pending request not yet expired
                $_SESSION['flash']['error'] = 'This account has already a pending password reset request.';
            break;

            default:
                $logger->info('Unknown answer from send_mail method');
                $_SESSION['flash']['error'] = 'Invalid type';
                
        }
        $this->new();
    }

    // GET /storyforge/create_password.php
    function choose_new_password() {
        $logger = getLogger('choose new password');
        $logger->info('GET /storyforge/create_password.php');

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to access the password creation page but is already authenticated");
            $_SESSION['flash']['error'] = 'You are already authenticated.';
            header("Location: ". "login.php");

            return;
        }

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("create_password", ["flash" => $flash, "password_minlength" => Validator::PASSWORD_MIN_LENGTH]);
    }

    // POST /storyforge/create_password.php
    function set_new_password() {
        $logger = getLogger('set new password');
        $logger->info('POST /storyforge/create_password.php');

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to reset their password but is already authenticated");
            $_SESSION['flash']['error'] = 'You are already authenticated.';
            header("Location: ". "login.php");

            return;
        }

        $code                = $this->params['POST']['code'];
        $password            = $this->params['POST']['password'];
        $password_confirm    = $this->params['POST']['password_confirm'];
        if(!isset($code, $password, $password_confirm)) {
            $logger->info("User tried to reset their password without setting all parameters");
            $_SESSION['flash']['error'] = 'Compile all fields.';
            $this->new();

            return;
        }


        $_SESSION['flash']['info'] = 'This feature is not yet implemented.';
        header("Location: ". "/");
    }
}