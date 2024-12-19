<?php
namespace App\Controllers;

require_once __DIR__ . '/../models/User.php';

require_once __DIR__ . '/../libs/utils/mail/send_mail.php';

require_once __DIR__ . '/../libs/utils/log/logger.php';

use App\Models\User;

class HomeController
{
    public function index()
    {
        $userModel = new User();
        $users = $userModel->getAllUsers();

        $logger = getLogger('home');

        $logger->debug('This is a debug message');

        # echo sendEmail("leonardo.pantani@gmail.com", "This is a test", "<h1>Hello</h1><p>How are you?</p>");

        include __DIR__ . '/../views/home.php';
    }
}
?>