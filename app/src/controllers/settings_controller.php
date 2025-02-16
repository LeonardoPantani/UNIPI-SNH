<?php

namespace App\Controllers;

require_once __DIR__ . '/../libs/utils/validator/validator.php';
require_once __DIR__ . '/../libs/utils/db/DBConnection.php';
require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../libs/utils/view/ViewManager.php';
require_once __DIR__ . '/../libs/utils/config/constants.php';

use App\Models\User;
use App\Utils\ViewManager;
use App\Utils\Validator;

class SettingsController {
    // GET /settings
    public function new(): void
    {
        $logger = getLogger('settings');
        $logger->info('GET /user/settings');

        if(!isset($_SESSION["user"])) {
            $logger->info("User tried to access settings page but is not authenticated");
            $_SESSION['flash']['error'] = 'You are not authenticated.';
            header('Location: '. LOGIN_PATH);
            
            return;
        }

        $user = User::getUserById($_SESSION["user"]);

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("settings", ["flash" => $flash, "username" => $user->getUsername(), "email" => $user->getEmail(), "email_pattern" => Validator::EMAIL_REGEX, "username_pattern" => Validator::USERNAME_REGEX_HTML, "username_minlength" => Validator::USERNAME_MIN_LENGTH, "username_maxlength" => Validator::USERNAME_MAX_LENGTH, "password_minlength" => Validator::PASSWORD_MIN_LENGTH]);
    }

    // POST /user/settings
    public function settings_change($params_post): void
    {
        $logger = getLogger('settings update');
        $logger->info('POST /user/settings');

        if(!isset($_SESSION["user"])) {
            $logger->info("User tried to change their password but are not authenticated");
            $_SESSION['flash']['error'] = 'You are not authenticated.';
            header('Location: ' . ROOT_PATH);

            return;
        }

        if(!isset($params_post['password_old']) || !Validator::passwordValidation($params_post['password_old'])) {
            $logger->info('Invalid old password');
            $_SESSION['flash']['error'] = 'The old password must be at least '. Validator::PASSWORD_MIN_LENGTH .' chars long';
            $this->new();
            return;
        }

        if(!isset($params_post['password_new']) || !Validator::passwordValidation($params_post['password_new'])) {
            $logger->info('Invalid new password');
            $_SESSION['flash']['error'] = 'The old password must be at least '. Validator::PASSWORD_MIN_LENGTH .' chars long';
            $this->new();
            return;
        }

        if(!isset($params_post['password_new_confirm']) || !Validator::passwordValidation($params_post['password_new_confirm'])) {
            $logger->info('Invalid confirmation password');
            $_SESSION['flash']['error'] = 'The old password must be at least '. Validator::PASSWORD_MIN_LENGTH .' chars long';
            $this->new();
            return;
        }

        $password_old         = $params_post['password_old'];
        $password_new         = $params_post['password_new'];
        $password_new_confirm = $params_post['password_new_confirm'];

        if($password_new !== $password_new_confirm) {
            $logger->info('New password and new password confirm do not match');
            $_SESSION['flash']['error'] = 'New password and confirm new password do not match';
            $this->new();
            return;
        }
        
        $user = User::getUserById($_SESSION["user"]);

        if(!password_verify($password_old, $user->getPasswordHash())) {
            $logger->info("Username specified a wrong old password", ['username' => $user->getUsername()]);
            $_SESSION['flash']['error'] = 'Old password is not correct.';
            $this->new();
            return;
        }

        $password_new_hash = password_hash($password_new, PASSWORD_DEFAULT);
        if(User::updateUserPassword($user->getId(), $password_new_hash)) {
            $_SESSION['flash']['success'] = 'Your password has been changed.';

            $is_sent = sendEmail($user->getEmail(), "Password reset completed", "password_changed", ["username" => $user->getUsername()]);
            if($is_sent !== true) {
                $logger->info('Unable to send email notification for password change');
            }
        } else {
            $_SESSION['flash']['error'] = 'Ooops! Something went wrong while changing your password. Please try again later or contact support if the problem persists.';
        }
        header("Location: " . ROOT_PATH);
    }
}