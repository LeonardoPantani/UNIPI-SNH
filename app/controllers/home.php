<?php
namespace App\Controllers;

require_once __DIR__ . '/../models/User.php';

use App\Models\User;

class HomeController
{
    public function index()
    {
        $userModel = new User();
        $users = $userModel->getAllUsers();

        include __DIR__ . '/../views/home.php';
    }
}
?>