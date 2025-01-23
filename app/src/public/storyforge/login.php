<?php
require_once __DIR__ . '/../../controllers/login.php';

use App\Controllers\LoginController;
session_start();

$controller = new LoginController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->index();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
} else {
    http_response_code(405);
    echo "Method not allowed.";
}