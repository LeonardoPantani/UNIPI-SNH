<?php
require_once __DIR__ . '/../../controllers/user_controller.php';

use App\Controllers\UserController;
session_start();

$controller = new UserController($_SERVER, $_GET, $_POST);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->new();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->create();
} else {
    http_response_code(405);
    echo "Method not allowed.";
}

