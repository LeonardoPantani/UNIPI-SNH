<?php

namespace App\Controllers;

require_once __DIR__ . '/../libs/utils/validator/validator.php';
require_once __DIR__ . '/../libs/utils/db/DBConnection.php';
require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/AdminService.php';
require_once __DIR__ . '/../libs/utils/view/ViewManager.php';

use App\Models\User;
use App\Models\AdminService;
use App\Utils\ViewManager;
use App\Utils\Validator;

class AdminController {
    private array $server;
    private array $params;

    public function __construct(array $server, array $params_get, array $params_post) {
        $this->server = $server;

        $this->params = array(
            'GET'  => $params_get,
            'POST' => $params_post
        );
    }

    // GET /admin
    function panel() {
        $logger = getLogger('admin panel');
        $logger->info('GET /admin');

        if(!isset($_SESSION["user"]) || (isset($_SESSION["user"]) && $_SESSION["role"] != "admin")) {
            $logger->info("User tried to access the admin panel while not being authenticated");
            http_response_code(404);
            echo "Not found.";
            
            return;
        }

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        $admin_services = array(
            AdminService::newAdminServiceInstance("Edit User Role", "edit", "This service allows you to change the role of a user.", "wand-sparkles"),
            AdminService::newAdminServiceInstance("Placeholder"),
            AdminService::newAdminServiceInstance("Placeholder 2"),
            AdminService::newAdminServiceInstance("Placeholder 3"),
        );

        ViewManager::render("admin_panel", ["flash" => $flash, "admin_services" => $admin_services]);
    }

    // GET /admin/services/edit
    function edit_user() {
        $logger = getLogger('edit user');
        $logger->info('GET /admin/services/edit');

        if(!isset($_SESSION["user"]) || (isset($_SESSION["user"]) && $_SESSION["role"] != "admin")) {
            $logger->info("User tried to access the admin panel while not being authenticated");
            http_response_code(404);
            echo "Not found.";
            
            return;
        }

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("admin_edituser", ["flash" => $flash, "roles" => User::getRoles(), "username_pattern" => Validator::USERNAME_REGEX_HTML, "username_minlength" => Validator::USERNAME_MIN_LENGTH, "username_maxlength" => Validator::USERNAME_MAX_LENGTH]);
    }

    // POST /admin/edit_user.php
    function request_user_edit() {
        http_response_code(501);
        echo "Not implemented.";
    }
}