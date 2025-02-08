<?php

namespace App\Controllers;

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Novel.php';
require_once __DIR__ . '/../libs/utils/mail/sendmail.php';
require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../libs/utils/view/ViewManager.php';

use App\Models\User;
use App\Models\Novel;
use App\Utils\ViewManager;

class HomeController
{
    // GET /
    public function new()
    {
        $users = User::getAllUsers();

        $logger = getLogger('home');

        $logger->info('This is a debug message', ['users' => $users], ['extra information' => 'Nothing']);

        $novels = Novel::getAllNovels();

        #echo sendEmail("leonardo.pantani@gmail.com", "This is a test", "<h1>Hello</h1><p>How are you?</p>");

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("home", ["session" => $_SESSION, "flash" => $flash, "users" => $users, "novels" => $novels]);
    }
}
