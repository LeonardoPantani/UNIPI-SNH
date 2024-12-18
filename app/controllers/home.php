<?php
namespace App\Controllers;

require_once __DIR__ . '/../models/User.php';

require_once __DIR__ . '/../libs/utils/mail/send_mail.php';

use App\Models\User;

class HomeController
{
    public function index()
    {
        $userModel = new User();
        $users = $userModel->getAllUsers();

        # echo sendEmail("leonardo.pantani@gmail.com", "This is a test", "<h1>Hello</h1><p>How are you?</p>");

        include __DIR__ . '/../views/home.php';
    }
}
?>