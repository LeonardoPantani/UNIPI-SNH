<?php
require_once __DIR__ . '/../../controllers/session_controller.php';

use App\Controllers\LoginController;
session_start();

$controller = new LoginController($_SERVER, $_GET, $_POST);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->logout();
} else {
    header("Location: /storyforge/errors/405.php");
}