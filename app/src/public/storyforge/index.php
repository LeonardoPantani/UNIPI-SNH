<?php
require_once __DIR__ . '/../../controllers/home.php';

use App\Controllers\HomeController;
session_start();

$controller = new HomeController();
$controller->index();