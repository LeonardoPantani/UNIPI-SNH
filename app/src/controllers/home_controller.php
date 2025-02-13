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

class HomeController {

    // GET /
    public function new() {
        $logger = getLogger('HomeController');
        $logger->info('function: new');

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("home", ["flash" => $flash]);
    }
}
