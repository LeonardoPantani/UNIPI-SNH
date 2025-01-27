<?php
require_once __DIR__ . '/../../controllers/login.php';

use App\Controllers\LoginController;
session_start();

$controller = new LoginController($_SERVER, $_GET, $_POST);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->logout();
} else {
    http_response_code(405);
    echo "Method not allowed.";
}