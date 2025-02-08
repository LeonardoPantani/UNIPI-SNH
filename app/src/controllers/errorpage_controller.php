<?php

namespace App\Controllers;

require_once __DIR__ . '/../libs/utils/db/DBConnection.php';
require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../libs/utils/view/ViewManager.php';

use App\Utils\ViewManager;

class ErrorPageController {
    private array $server;
    private array $params;

    private const array ERROR_MESSAGES = [
        400 => "Oops! Something went wrong, but it was not your fault... probably.",
        401 => "Looks like you need a password to enter this party.",
        403 => "You are not on the guest list. Try again later.",
        404 => "But all is not lost.",
        405 => "Not the right tool for the job.",
        500 => "The server had a little meltdown. Please try again.",
        501 => "This feature is still a work in progress. Stay tuned!"
    ];

    public function __construct(array $server, array $params_get, array $params_post) {
        $this->server = $server;

        $this->params = array(
            'GET'  => $params_get,
            'POST' => $params_post
        );
    }

    // GET /errors/X.php
    function error(int $error_code = 500) {
        $logger = getLogger(' error page');
        $logger->info('GET /errors/'.$error_code.'.php');

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        http_response_code($error_code);
        ViewManager::render("error_page", ["flash" => $flash, "error_code" => $error_code, "error_message" => self::ERROR_MESSAGES[$error_code] ? : "But all is not lost."]);
    }
}