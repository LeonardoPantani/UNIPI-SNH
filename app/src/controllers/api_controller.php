<?php

namespace App\Controllers;

require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../libs/utils/view/ViewManager.php';
require_once __DIR__ . '/../libs/utils/validator/validator.php';
require_once __DIR__ . '/../models/User.php';

use App\Utils\ViewManager;
use App\Utils\Validator;
use App\Models\User;

class ApiController {
    // POST /api/v1/users
    public function searchUsers($params_post) {
        $logger = getLogger('api');
        $logger->info('POST /api/v1/users');

        $partial_username = $params_post['username'];
        
        if(!Validator::partialUsernameValidation($partial_username)) {
            $logger->info('Invalid partial username');

            $http_code = 400;
            $response = array(
                'response' => 'Username must be a non-empty string with length less than '. Validator::USERNAME_MAX_LENGTH . ' and can only contain letters, numbers, dashes and underscores'
            );
        } else {
            $users = User::getUsersByPartialUsername($partial_username);

            $http_code = 200;
            $response = array(
                'response' => array_map(fn($user) => $user->getUsername(), $users)
            );
        }

        header('Content-Type: application/json');
        http_response_code($http_code);
        echo json_encode($response);
    }
}