<?php

namespace App\Controllers;

require_once __DIR__ . '/../libs/utils/validator/validator.php';
require_once __DIR__ . '/../libs/utils/db/DBConnection.php';
require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ForgotPassword.php';
require_once __DIR__ . '/../libs/utils/view/ViewManager.php';
require_once __DIR__ . '/../libs/utils/config/constants.php';

use App\Models\User;
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

    // GET /password/reset
    function new() {
        $logger = getLogger('forgot password');
        $logger->info('GET /password/reset');

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to access the password reset page but is already authenticated");
            $_SESSION['flash']['error'] = 'You are already authenticated.';
            header('Location: ' . ROOT_PATH);
            
            return;
        }

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("forgot_password", ["flash" => $flash, "email_pattern" => Validator::EMAIL_REGEX]);
    }

    // POST /password/reset
    function validate_reset_request() {
        $logger = getLogger('validate reset request');
        $logger->info('POST /password/reset');

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to reset its password but is already authenticated");
            $_SESSION['flash']['error'] = 'You are already authenticated.';
            header('Location: ' . ROOT_PATH);

            return;
        }

        if(!isset($this->params['POST']['email'])) {
            $logger->info("User tried to reset their password without setting their email");
            $_SESSION['flash']['error'] = 'Insert your email.';
            $this->new();

            return;
        }
        $email = $this->params['POST']['email'];

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

    // GET /password/reset/:token
    function choose_new_password($params = "") {
        $logger = getLogger('choose new password');
        $logger->info('GET /password/reset/:token');

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to access the password creation page but is already authenticated");
            $_SESSION['flash']['error'] = 'You are already authenticated.';
            header('Location: ' . ROOT_PATH);

            return;
        }

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("create_password", ["flash" => $flash, "code" => isset($params['token']) ? $params['token'] : "", "password_minlength" => Validator::PASSWORD_MIN_LENGTH]);
    }

    // POST /password/reset/:token
    function set_new_password() {
        $logger = getLogger('set new password');
        $logger->info('POST /password/reset/:token');

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to reset their password but is already authenticated");
            $_SESSION['flash']['error'] = 'You are already authenticated.';
            header('Location: ' . ROOT_PATH);

            return;
        }

        $code                = $this->params['POST']['code'];
        $password            = $this->params['POST']['password'];
        $password_confirm    = $this->params['POST']['password_confirm'];
        if(!isset($code, $password, $password_confirm)) {
            $logger->info("User tried to reset their password without setting all parameters");
            $_SESSION['flash']['error'] = 'Compile all fields.';
            $this->choose_new_password();
            return;
        }

        if(!Validator::passwordValidation($password)) {
            $logger->info('Invalid password');
            $_SESSION['flash']['error'] = 'The password must be at least '. Validator::PASSWORD_MIN_LENGTH .' chars long';
            $this->choose_new_password();
            return;
        }

        if($password != $password_confirm) {
            $logger->info('Invalid confirm password');
            $_SESSION['flash']['error'] = 'Mismatch between password and password confirm';
            $this->choose_new_password();
            return;
        }

        $user_id = ForgotPassword::get_userid_by_code($code);
        if(empty($user_id)) { // invalid code
            $logger->info('Invalid code');
            $_SESSION['flash']['error'] = 'The verification code you entered is not correct';
            $this->choose_new_password();
            return;
        }
        $user_id = $user_id["user_id"];

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $res = User::updateUserPassword($user_id, $password_hash);
        if(!$res) {
            $logger->info('Database error during password change');
            $_SESSION['flash']['error'] = 'Oops. Something went wrong on our end.';
            $this->choose_new_password();
            return;
        }

        $res = ForgotPassword::delete_code($user_id);
        if(!$res) {
            $logger->info('Database error during password change');
        }

        $user = User::getUserById($user_id);
        if($user != null) {
            $is_sent = sendEmail($user->getEmail(), "Password reset completed", "password_changed", ["username" => $user->getUsername()]);
            if($is_sent !== true) {
                $logger->info('Unable to send email notification for password change');
            }
        }

        $_SESSION['flash']['success'] = 'Your password has been correctly updated.';
        header('Location: ' . LOGIN_PATH);
    }
}