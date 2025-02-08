<?php
require_once __DIR__ . '/../../../controllers/errorpage_controller.php';

use App\Controllers\ErrorPageController;
session_start();

$controller = new ErrorPageController($_SERVER, $_GET, $_POST, $_FILES);

$controller->error(405);