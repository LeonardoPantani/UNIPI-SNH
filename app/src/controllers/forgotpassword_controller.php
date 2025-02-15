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
    // GET /password/reset
    public function new() {
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

        ViewManager::render("forgot_password", ["flash" => $flash]);
    }

    // POST /password/reset
    public function validate_reset_request($params_post) {
        $logger = getLogger('validate reset request');
        $logger->info('POST /password/reset');

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to reset its password but is already authenticated");
            $_SESSION['flash']['error'] = 'You are already authenticated.';
            header('Location: ' . ROOT_PATH);

            return;
        }

        if(!isset($params_post['email']) || !Validator::emailValidation($params_post['email'])) {
            $logger->info("User tried to reset their password without setting their email");
            $_SESSION['flash']['error'] = 'Invalid email';
            $this->new();

            return;
        }

        $email = $params_post['email'];
        $requestStatus = ForgotPassword::send_mail($email);

        switch($requestStatus) {
            case 0: // ok
                $logger->info('Email sent');
                $_SESSION['flash']['success'] = 'If there is an account with that email address, you will receive an email with further instructions on how to reset your password.';
            break;

            case 1: // cannot send email
                $logger->info('Error during email sending');
                $_SESSION['flash']['error'] = 'Ooops! Something went wrong while sending the password recovery email. Please try again later or contact support if the problem persists.';
            break;

            case 2: // pending request not yet expired
                $logger->info('Pending request found');
                $_SESSION['flash']['error'] = 'This account has already a pending password reset request.';
            break;

            default:
                $logger->info('Unknown answer from send_mail method');
                $_SESSION['flash']['error'] = 'Invalid type';
                
        }

        header('Location: ' . ROOT_PATH);
    }

    // GET /password/reset/:code
    public function choose_new_password($params_path) {
        $logger = getLogger('choose new password');
        $logger->info('GET /password/reset/:code');

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to access the password creation page but is already authenticated");
            $_SESSION['flash']['error'] = 'You are already authenticated.';
            header('Location: ' . ROOT_PATH);

            return;
        }

        if(!isset($params_path['code']) || !Validator::codeValidation($params_path['code'])) {
            $logger->info("Invalid code");
            $_SESSION['flash']['error'] = 'Invalid code';
            header('Location: ' . ROOT_PATH);

            return;
        }

        $code = $params_path['code'];

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("create_password", ["flash" => $flash, "code" => $code, "password_minlength" => Validator::PASSWORD_MIN_LENGTH]);
    }

    // POST /password/reset/:code
    function set_new_password($params_path, $params_post) {
        $logger = getLogger('set new password');
        $logger->info('POST /password/reset/:code');

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to reset their password but is already authenticated");
            $_SESSION['flash']['error'] = 'You are already authenticated.';
            header('Location: ' . ROOT_PATH);

            return;
        }

        if(!isset($params_path['code']) || !Validator::codeValidation($params_path['code'])) {
            $logger->info("Invalid code");
            $_SESSION['flash']['error'] = 'Invalid code';
            header('Location: ' . ROOT_PATH);
            return;
        }

        if(!isset($params_post['password']) || !Validator::passwordValidation($params_post['password'])) {
            $logger->info('Invalid password');
            $_SESSION['flash']['error'] = 'The password must be at least '. Validator::PASSWORD_MIN_LENGTH .' chars long';
            $this->choose_new_password($params_path);
            return;
        }

        if(!isset($params_post['password_confirm']) || !Validator::passwordValidation($params_post['password_confirm'])) {
            $logger->info('Invalid confirmation password');
            $_SESSION['flash']['error'] = 'The password must be at least '. Validator::PASSWORD_MIN_LENGTH .' chars long';
            $this->choose_new_password($params_path);
            return;
        }

        $code             = $params_path['code'];
        $password         = $params_post['password'];
        $password_confirm = $params_post['password_confirm'];

        if($password != $password_confirm) {
            $logger->info('Invalid confirm password');
            $_SESSION['flash']['error'] = 'Mismatch between password and password confirm';
            $this->choose_new_password($params_path);
            return;
        }

        $user_id = ForgotPassword::get_userid_by_code($code);
        
        if(empty($user_id)) { // invalid code
            $logger->info('Invalid code');
            $_SESSION['flash']['error'] = 'The verification code you entered is not correct';
            $this->choose_new_password($params_path);
            return;
        }
        
        $user_id = $user_id["user_id"];

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $res = User::updateUserPassword($user_id, $password_hash);

        if(!$res) {
            $logger->info('Database error during password change');
            $_SESSION['flash']['error'] = 'Oops. Something went wrong on our end.';
            $this->choose_new_password($params_path);
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