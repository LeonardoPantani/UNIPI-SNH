<?php

namespace App\Controllers;

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../libs/utils/validator/validator.php';
require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../libs/utils/view/ViewManager.php';
require_once __DIR__ . '/../libs/utils/config/constants.php';

use App\Models\User;
use App\Utils\Validator;
use App\Utils\ViewManager;
use Redis;

class LoginController {
    // GET /login
    public function new(): void
    {
        $logger = getLogger('login');
        $logger->info('GET /login');

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to access login page but is already authenticated");
            $_SESSION['flash']['error'] = 'You are already authenticated.';
            header('Location: ' . ROOT_PATH);
            return;
        }

        $token = $_SESSION["token"];
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("login", ["flash" => $flash, "token" => $token, "username_pattern" => Validator::USERNAME_REGEX_HTML, "username_minlength" => Validator::USERNAME_MIN_LENGTH, "username_maxlength" => Validator::USERNAME_MAX_LENGTH, "password_minlength" => Validator::PASSWORD_MIN_LENGTH, "password_pattern" => Validator::PASSWORD_REGEX_HTML]);
    }

    // POST /login
    public function login($params_post): void
    {
        $logger = getLogger('login');
        $logger->info('POST /login');

        if(!isset($params_post["token"]) || $params_post["token"] !== $_SESSION["token"]) {
            $logger->info('Invalid CSRF token');
            $_SESSION['flash']['error'] = 'Invalid CSRF token';
            $this->new();
            return;
        }

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to login but is already authenticated", ['user_id' => $_SESSION["user"]]);
            $_SESSION['flash']['error'] = 'You are already authenticated.';
            header('Location: ' . ROOT_PATH);
            return;
        }

        if(!isset($params_post["username"]) || !Validator::usernameValidation($params_post["username"])) {
            $logger->info('Invalid username');
            $_SESSION['flash']['error'] = 'Username length must be at least '. Validator::USERNAME_MIN_LENGTH .' chars and less than '. Validator::USERNAME_MAX_LENGTH . ' and can only contain letters, numbers, dashes and underscores.';
            $this->new();
            return;
        }

        if(!isset($params_post["password"]) || !Validator::passwordValidation($params_post["password"])) {
            $logger->info('Invalid password');
            $_SESSION['flash']['error'] = 'The password must be at least '. Validator::PASSWORD_MIN_LENGTH .' chars long';
            $this->new();
            return;
        }

        $username = $params_post["username"];
        $password = $params_post["password"];

        $redis = new Redis([
            'host' => getenv('REDIS_HOST'),
            'port' => intval(getenv('REDIS_PORT')),
            'connectTimeout' => 2.5
        ]);

        $failures = 0;
        if($redis->exists($username)) {
            $failures = intval($redis->get($username));
        }

        if($failures >= 5) {
            $logger->info("Too many failed attemps", ['username' => $username]);
            $redis->expire($username, 5 * 60);

            $_SESSION['flash']['error'] = 'Too many failed attemps. Retry later';
            $this->new();
            return;
        }

        $user = User::getUserByUsername($username);

        if(is_null($user)) {
            $logger->info("A user used an invalid username", ['username' => $username]);

            $failures = $redis->incr($username);
            if($failures < 5) {
                $expire = 1 * 60;
                $error = 'Invalid username or password.';
            } else {
                $expire = 5 * 60;
                $error = 'Too many failed attemps. Retry later';
            }
            
            $redis->expire($username, $expire);
            $_SESSION['flash']['error'] = $error;
            $this->new();
            return;
        }

        if(!password_verify($password, $user->getPasswordHash())) {
            $logger->info("User with username specified a wrong password", ['username' => $username]);

            $failures = $redis->incr($username);
            if($failures < 5) {
                $expire = 1 * 60;
                $error = 'Invalid username or password.';
            } else {
                $expire = 5 * 60;
                $error = 'Too many failed attemps. Retry later';
            }
            
            $redis->expire($username, $expire);
            $_SESSION['flash']['error'] = $error;
            $this->new();
            return;
        }

        $redis->del($username);
        session_regenerate_id(true);
        $_SESSION["user"] = $user->getId();
        $_SESSION["username"] = $user->getUsername();
        $_SESSION["role"] = $user->getRoleName();
        $_SESSION["token"] = bin2hex(random_bytes(32));
        $_SESSION['flash']['success'] = 'Authenticated as '. $_SESSION["username"] . '.';

        header('Location: ' . ROOT_PATH);
    }

    public function logout(): void
    {
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

        header('Location: ' . ROOT_PATH);
    }
}
