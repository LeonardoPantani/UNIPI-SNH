<?php

namespace App\Controllers;

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../libs/utils/validator/validator.php';
require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../libs/utils/view/ViewManager.php';

use App\Models\User;
use App\Utils\Validator;
use App\Utils\ViewManager;

class LoginController
{
    private array $server;
    private array $params;

    public function __construct(array $server, array $params_get, array $params_post) {
        $this->server = $server;

        $this->params = array(
            'GET'  => $params_get,
            'POST' => $params_post
        );
    }

    // GET /storyforge/login.php
    public function new()
    {
        $users = User::getAllUsers();

        $logger = getLogger('login');

        $logger->info('This is a debug message', ['users' => $users], ['extra information' => 'Nothing']);

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("login", ["flash" => $flash, "username_pattern" => Validator::USERNAME_REGEX_HTML, "username_minlength" => Validator::USERNAME_MIN_LENGTH, "username_maxlength" => Validator::USERNAME_MAX_LENGTH, "password_minlength" => Validator::PASSWORD_MIN_LENGTH]);
    }

    // POST /storyforge/login.php
    public function login() {
        $logger = getLogger('login');
        $logger->info('POST /storyforge/login.php');

        $username = $this->params["POST"]["username"];
        $password = $this->params["POST"]["password"];

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to login but is already authenticated", ['username' => $username]);
            $_SESSION['flash']['error'] = 'You are already authenticated.';
            header("Location: ". "./");
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

        $user = User::getUserByUsername($username);

        if(is_null($user)) {
            $logger->info("A user used an invalid username", ['username' => $username]);
            $_SESSION['flash']['error'] = 'Invalid username or password.';
            $this->new();
            return;
        }

        if(!password_verify($password, $user->getPasswordHash())) {
            $logger->info("User with username specified a wrong password", ['username' => $username]);
            $_SESSION['flash']['error'] = 'Invalid username or password.';
            $this->new();
            return;
        }

        session_regenerate_id(true);
        $_SESSION["user"] = $user->getId();
        $_SESSION['flash']['success'] = 'Authenticated as <b>'. $user->getUsername() . '</b>.';

        header("Location: ". "/");
    }

    public function logout() {
        $logger = getLogger('logout');

        if(!isset($_SESSION["user"])) {
            $logger->info("User tried to logout but is not authenticated");
            $_SESSION['flash']['error'] = 'You are not authenticated.';
            $this->new();
            return;
        }

        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );

        session_unset();
        session_destroy();

        header("Location: ". "/");
    }
}
