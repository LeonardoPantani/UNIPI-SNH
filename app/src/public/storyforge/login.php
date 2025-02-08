<?php
require_once __DIR__ . '/../../controllers/session_controller.php';

use App\Controllers\LoginController;
session_start();

$controller = new LoginController($_SERVER, $_GET, $_POST);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->new();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
} else {
    header("Location: /storyforge/errors/405.php");
}