<?php
require_once __DIR__ . '/../../controllers/novel_controller.php';

use App\Controllers\NovelController;
session_start();

$controller = new NovelController($_SERVER, $_GET, $_POST, $_FILES);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->new();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->create();
} else {
    header("Location: /storyforge/errors/405.php");
}