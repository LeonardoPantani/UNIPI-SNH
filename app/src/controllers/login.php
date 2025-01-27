<?php

namespace App\Controllers;

require_once __DIR__ . '/../models/User.php';

require_once __DIR__ . '/../libs/utils/log/logger.php';

use App\Models\User;

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

    public function index()
    {
        $users = User::getAllUsers();

        $logger = getLogger('login');

        $logger->info('This is a debug message', ['users' => $users], ['extra information' => 'Nothing']);

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        include __DIR__ . '/../views/login.php';
    }

    public function login()
    {
        $logger = getLogger('login');

        $username = $this->params["POST"]["username"];
        $password = $this->params["POST"]["password"];

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to login but is already authenticated", ['username' => $username]);
            header("Location: ". "login.php?e=3");
            return;
        }

        $user = User::getUserByUsername($username);

        if(is_null($user)) {
            $logger->info("A user used an invalid username", ['username' => $username]);
            header("Location: ". "login.php?e=1");
            return;
        }

        if(!password_verify($password, $user->getPasswordHash())) {
            $logger->info("User with username specified a wrong password", ['username' => $username]);
            header("Location: ". "login.php?e=1");
            return;
        }

        session_regenerate_id(true);
        $_SESSION["user"] = $user->getId();

        header("Location: ". "/");
    }

    public function logout() {
        if(!isset($_SESSION["user"])) {
            $logger->info("User tried to login but is not authenticated");
            header("Location: ". "login.php");

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
