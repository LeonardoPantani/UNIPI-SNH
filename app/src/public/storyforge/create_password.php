<?php
require_once __DIR__ . '/../../controllers/forgotpassword_controller.php';

use App\Controllers\ForgotPasswordController;
session_start();

$controller = new ForgotPasswordController($_SERVER, $_GET, $_POST);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->choose_new_password();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->set_new_password();
} else {
    http_response_code(405);
    echo "Method not allowed.";
}