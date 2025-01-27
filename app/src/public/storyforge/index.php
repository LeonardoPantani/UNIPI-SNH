<?php
require_once __DIR__ . '/../../controllers/home_controller.php';

use App\Controllers\HomeController;
session_start();

$controller = new HomeController();
$controller->new();