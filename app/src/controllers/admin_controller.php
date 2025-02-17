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

class AdminController {
    // GET /admin
    public function panel(): void
    {
        $logger = getLogger('admin panel');
        $logger->info('GET /admin');

        if(!isset($_SESSION["user"]) || ($_SESSION["role"] !== "admin")) {
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
    public function edit_user(): void
    {
        $logger = getLogger('edit user');
        $logger->info('GET /admin/services/edit');

        if(!isset($_SESSION["user"]) || ($_SESSION["role"] !== "admin")) {
            $logger->info("User tried to access the admin panel while not being authenticated");
            $controller = new ErrorPageController();
            $controller->error(404);
            
            return;
        }

        $token = $_SESSION['token'];
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("admin_edituser", ["flash" => $flash, "token" => $token, "roles" => User::getNonAdminRoles(), "username_pattern" => Validator::USERNAME_REGEX_HTML, "username_minlength" => Validator::USERNAME_MIN_LENGTH, "username_maxlength" => Validator::USERNAME_MAX_LENGTH]);
    }

    // POST /admin/services/edit
    function request_user_edit($params_post): void
    {
        $logger = getLogger('edit user');
        $logger->info('POST /admin/services/edit');

        if(!isset($_SESSION["user"]) || ($_SESSION["role"] !== "admin")) {
            $logger->info("User tried to access the admin panel while not being authenticated");
            $controller = new ErrorPageController();
            $controller->error(404);
            
            return;
        }

        if(!isset($params_post["token"]) || $params_post["token"] !== $_SESSION["token"]) {
            $logger->info('Invalid CSRF token');
            $_SESSION['flash']['error'] = 'Invalid CSRF token';
            $this->edit_user();
            return;
        }

        if(!isset($params_post["username"]) || !Validator::usernameValidation($params_post["username"])) {
            $logger->info('Invalid edit user role username');
            $_SESSION['flash']['error'] = 'Invalid username';
            $this->edit_user();

            return;
        }

        $valid_roles_ids = [];
        foreach(User::getNonAdminRoles() as $roleType) {
            $valid_roles_ids[] = $roleType["id"];
        }

        if(!isset($params_post["role"]) || !in_array($params_post["role"], $valid_roles_ids)) {
            $logger->info('Invalid user role');
            $_SESSION['flash']['error'] = 'Invalid user role';
            $this->edit_user();

            return;
        }

        $role = intval($params_post["role"]);
        $username = $params_post["username"];
        
        $user = User::getUserByUsername($username);

        if(is_null($user)) {
            $logger->info('User not found');
            $_SESSION['flash']['error'] = 'User not found';
            $this->edit_user();
            
            return;
        }

        if($user->getRoleName() === 'admin') {
            $logger->info('Cannot change the role of an admin user');
            $_SESSION['flash']['error'] = 'Cannot change the role of an admin user';
            $this->edit_user();
            
            return;
        }

        $res = User::updateUserRole($user->getId(), $role);

        if(!$res) {
            $logger->info('Database error during role updating');
            $_SESSION['flash']['error'] = 'An error occured during role updating';
            $this->edit_user();
            
            return;
        }

        $logger->info('Updated user role', ["username" => $username, "role" => $role]);
        $_SESSION['flash']['success'] = 'Successfully updated role';

        header('Location: ' . ADMIN_EDIT_USER_PATH);
    }
}