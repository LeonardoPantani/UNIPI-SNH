<?php

namespace App\Controllers;

require_once __DIR__ . '/../models/User.php';

require_once __DIR__ . '/../libs/utils/log/logger.php';

use App\Models\User;

class LoginController
{
    public function index()
    {
        $users = User::getAllUsers();

        $logger = getLogger('login');

        $logger->info('This is a debug message', ['users' => $users], ['extra information' => 'Nothing']);

        include __DIR__ . '/../views/login.php';
    }

    public function login()
    {
        $logger = getLogger('login');

        if(isset($_SESSION["user"])) {
            $logger->info("User tried to login but is already authenticated", ['username' => $_POST['username']]);
            header("Location: ". "login.php?e=3");
            return;
        }

        $username = $_POST["username"];
        $password = $_POST["password"];

        
        if(!User::usernameExists($username)) {
            $logger->info("A user used an invalid username", ['username' => $username]);
            header("Location: ". "login.php?e=1");
            return;
        }

        $user = User::authenticate($username, $password);

        if(is_null($user)) {
            $logger->info("User with username specified a wrong password", ['username' => $username]);
            header("Location: ". "login.php?e=1");
            return;
        }

        $_SESSION["user"] = $user;
        header("Location: ". "/");
    }
}
