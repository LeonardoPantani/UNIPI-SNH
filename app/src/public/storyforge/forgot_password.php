<?php
require_once __DIR__ . '/../../controllers/forgotpassword_controller.php';

use App\Controllers\ForgotPasswordController;
session_start();

$controller = new ForgotPasswordController($_SERVER, $_GET, $_POST);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->new();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->validate_reset_request();
} else {
    http_response_code(405);
    echo "Method not allowed.";
}