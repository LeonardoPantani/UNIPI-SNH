<?php
require_once __DIR__ . '/../../controllers/home.php';

use App\Controllers\HomeController;

$controller = new HomeController();
$controller->index();
?>