<?php

namespace App\Controllers;

require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../libs/utils/view/ViewManager.php';
require_once __DIR__ . '/../libs/utils/validator/validator.php';
require_once __DIR__ . '/../models/User.php';

use App\Utils\Validator;
use App\Models\User;
use App\Utils\ViewManager;

class ApiController {
    // POST /api/v1/users
    public function searchUsers($params_post) {
        $logger = getLogger('api');
        $logger->info('POST /api/v1/users');

        if(!isset($_SESSION["user"]) || ($_SESSION["role"] != "admin")) {
            $logger->info('Tried calling autocomplete api while not being logged or while not being admin');
            ViewManager::renderJson(array(
                'response' => 'Unauthorized'
            ));

            return;
        }

        if(!isset($params_post["token"]) || $params_post["token"] !== $_SESSION["token"]) {
            $logger->info('Invalid CSRF token');
            ViewManager::renderJson(array(
                'response' => 'Invalid CSRF token'
            ));

            return;
        }
        
        if(!isset($params_post['username']) || !Validator::partialUsernameValidation($params_post['username'])) {
            $logger->info('Invalid partial username');
            ViewManager::renderJson(array(
                'response' => 'Username must be a non-empty string with length less than '. Validator::USERNAME_MAX_LENGTH . ' and can only contain letters, numbers, dashes and underscores'
            ));
            
            return;
        } 

        $partial_username = $params_post['username'];
        $users = User::getNonAdminUsersByPartialUsername($partial_username);

        ViewManager::renderJson(array(
            'response' => array_map(fn($user) => $user->getUsername(), $users)
        ));
    }
}