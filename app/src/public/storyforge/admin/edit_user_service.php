<?php
require_once __DIR__ . '/../../../controllers/admin_controller.php';

use App\Controllers\AdminController;
session_start();

$controller = new AdminController($_SERVER, $_GET, $_POST);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->edit_user();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->request_user_edit();
} else {
    header("Location: /storyforge/errors/405.php");
}