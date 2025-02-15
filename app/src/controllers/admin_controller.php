<?php

namespace App\Controllers;

require_once __DIR__ . '/../libs/utils/validator/validator.php';
require_once __DIR__ . '/../libs/utils/db/DBConnection.php';
require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../libs/utils/view/ViewManager.php';
require_once __DIR__ . '/../controllers/errorpage_controller.php';

use App\Models\User;
use App\Utils\ViewManager;
use App\Utils\Validator;
use App\Controllers\ErrorPageController;

class AdminController {
    // GET /admin
    public function panel() {
        $logger = getLogger('admin panel');
        $logger->info('GET /admin');

        if(!isset($_SESSION["user"]) || (isset($_SESSION["user"]) && $_SESSION["role"] != "admin")) {
            $logger->info("User tried to access the admin panel while not being authenticated");
            $controller = new ErrorPageController();
            $controller->error(404);
            
            return;
        }

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("admin_panel", ["flash" => $flash]);
    }

    // GET /admin/services/edit
    public function edit_user() {
        $logger = getLogger('edit user');
        $logger->info('GET /admin/services/edit');

        if(!isset($_SESSION["user"]) || (isset($_SESSION["user"]) && $_SESSION["role"] != "admin")) {
            $logger->info("User tried to access the admin panel while not being authenticated");
            $controller = new ErrorPageController();
            $controller->error(404);
            
            return;
        }

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("admin_edituser", ["flash" => $flash, "roles" => User::getNonAdminRoles(), "username_pattern" => Validator::USERNAME_REGEX_HTML, "username_minlength" => Validator::USERNAME_MIN_LENGTH, "username_maxlength" => Validator::USERNAME_MAX_LENGTH]);
    }

    // POST /admin/services/edit
    function request_user_edit($params_post) {
        $logger = getLogger('edit user');
        $logger->info('POST /admin/services/edit');

        if(!isset($_SESSION["user"]) || (isset($_SESSION["user"]) && $_SESSION["role"] != "admin")) {
            $logger->info("User tried to access the admin panel while not being authenticated");
            $controller = new ErrorPageController();
            $controller->error(404);
            
            return;
        }

        if(!isset($params_post["username"]) || !User::usernameExists($params_post["username"])) {
            $logger->info('Invalid edit user role username');
            $_SESSION['flash']['error'] = 'Invalid username';
            $this->edit_user();
            return;
        }

        $valid_roles_ids = [];
        foreach(User::getNonAdminRoles() as $roleType) {
            array_push($valid_roles_ids, $roleType["id"]);
        }

        if(!isset($params_post["role"]) || !in_array($params_post["role"], $valid_roles_ids)) {
            $logger->info('Invalid user role');
            $_SESSION['flash']['error'] = 'Invalid user role';
            $this->edit_user();
            return;
        }

        User::updateUserRole(User::getUserByUsername($params_post["username"])->getId(), intval($params_post["role"]));

        $logger->info('Updated user role for username for role', ["username" => $params_post["username"], "role" => $params_post["role"]]);
        $_SESSION['flash']['success'] = 'Successfully updated role';

        $this->edit_user();
    }
}