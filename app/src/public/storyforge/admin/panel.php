<?php
require_once __DIR__ . '/../../../controllers/admin_controller.php';

use App\Controllers\AdminController;
session_start();

$controller = new AdminController($_SERVER, $_GET, $_POST);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->panel();
} else {
    http_response_code(405);
    echo "Method not allowed.";
}