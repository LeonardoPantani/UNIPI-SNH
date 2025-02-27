<?php
    require_once __DIR__ . '/../controllers/home_controller.php';
    require_once __DIR__ . '/../controllers/session_controller.php';
    require_once __DIR__ . '/../controllers/user_controller.php';
    require_once __DIR__ . '/../controllers/novel_controller.php';
    require_once __DIR__ . '/../controllers/forgotpassword_controller.php';
    require_once __DIR__ . '/../controllers/admin_controller.php';
    require_once __DIR__ . '/../controllers/api_controller.php';
    require_once __DIR__ . '/../controllers/errorpage_controller.php';
    require_once __DIR__ . '/../controllers/settings_controller.php';

    use App\controllers\HomeController;
    use App\controllers\LoginController;
    use App\controllers\UserController;
    use App\controllers\NovelController;
    use App\controllers\ForgotPasswordController;
    use App\controllers\AdminController;
    use App\controllers\ApiController;
    use App\controllers\SettingsController;
    use App\controllers\ErrorPageController;
    
    /*
        lifetime: 3600 seconds (i.e. 1 hour)
        domain:   $_SERVER['HTTP_HOST'] (i.e. '127.0.0.1' or 'localhost', since a cookie for 127.0.0.1 is not valid for localhost and viceversa)
        secure:   cookie is sent only on https
        httponly: javascript cannot access cookie, for example through the Document.cookie property
        samesite: 
            strict: browser sends the cookie only for same-site requests. If a request originates from a different domain or scheme, cookie is not sent 
            lax:    default behaviour if samesite is not set. Cookie is not sent on cross-site requests (e.g. requests to load images or frames), 
                    but is sent when a user is navigating to the origin site from an external site (e.g. when following a link)
            none:   browser sends the cookie with both cross-site and same-site requests
    */
    session_set_cookie_params([
        'lifetime' => 3600,
        'path'     => '/',
        'domain'   => $_SERVER['HTTP_HOST'],
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'strict'
    ]);

    session_start();

    if (!isset($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }

    $request = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];

    switch(true) {
        // GET|POST /
        case (bool) preg_match('/^\/?$/', $request):
            $controller = new HomeController();
            $controller->new();

            break;

        // GET|POST /registration
        case (bool) preg_match('/^\/registration\/?$/', $request):
            $controller = new UserController();

            switch($method) {
                case 'GET':
                    $controller->new();
                    break;

                case 'POST':
                    $controller->create($_POST);
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }

            break;

        // GET|POST /login
        case (bool) preg_match('/^\/login\/?$/', $request):
            $controller = new LoginController();

            switch($method) {
                case 'GET':
                    $controller->new();
                    break;

                case 'POST':
                    $controller->login($_POST);
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }

            break;

        // GET /logout
        case (bool) preg_match('/^\/logout\/?$/', $request):
            $controller = new LoginController();

            switch($method) {
                case 'GET':
                    $controller->logout();
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }

            break;

        // GET|POST /settings
        case (bool) preg_match('/^\/user\/settings\/?$/', $request):
            $controller = new SettingsController();

            switch($method) {
                case 'GET':
                    $controller->new();
                    break;

                case 'POST':
                    $controller->settings_change($_POST);
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }

            break;

        // GET|POST /password/forgot
        case (bool) preg_match('/^\/password\/forgot\/?$/', $request):
            $controller = new ForgotPasswordController();

            switch($method) {
                case 'GET':
                    $controller->new();
                    break;

                case 'POST':
                    $controller->validate_reset_request($_POST);
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }

            break;

        // GET|POST /password/reset/:code
        case (bool) preg_match('/^\/password\/reset(?:\/([0-9a-zA-Z]{0,5})|())$/', $request, $matches):
            $params_path = [
                "code" => $matches[1]
            ];

            $controller = new ForgotPasswordController();

            switch($method) {
                case 'GET':
                    $controller->choose_new_password($params_path);
                    break;

                case 'POST':
                    $controller->set_new_password($params_path, $_POST);
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }

            break;

        // GET|POST /novel/add
        case (bool) preg_match('/^\/novel\/add\/?$/', $request):
            $controller = new NovelController();

            switch($method) {
                case 'GET':
                    $controller->new();
                    break;

                case 'POST':
                    $controller->create($_POST, $_FILES);
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }
            
            break;

        // GET /novels
        case (bool) preg_match('/^\/novels\/?$/', $request):
            $controller = new NovelController();

            switch($method) {
                case 'GET':
                    $controller->showAll();
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }
            
            break;

        // GET /novels/:uuid
        case (bool) preg_match('/^\/novels\/([0-9a-z]{8}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{12})\/?$/', $request, $matches):
            $params_path = [
                "uuid" => $matches[1]
            ];

            $controller = new NovelController();

            switch($method) {
                case 'GET':
                    $controller->show($params_path);
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }
            
            break;

            // GET /user/novels
            case (bool) preg_match('/^\/user\/novels\/?$/', $request):
                $controller = new NovelController();
    
                switch($method) {
                    case 'GET':
                        $controller->showUser();
                        break;
    
                    default:
                        $controller = new ErrorPageController();
                        $controller->error(405);
                }
                
                break;

        // GET /admin
        case (bool) preg_match('/^\/admin\/?$/', $request):
            $controller = new AdminController();

            switch($method) {
                case 'GET':
                    $controller->panel();
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }

            break;

        // GET|POST /admin/services/edit
        case (bool) preg_match('/^\/admin\/services\/edit\/?$/', $request):
            $controller = new AdminController();

            switch($method) {
                case 'GET':
                    $controller->edit_user();
                    break;

                case 'POST':
                    $controller->request_user_edit($_POST);
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }
            
            break;

        // POST /api/v1/users
        case (bool) preg_match('/^\/api\/v1\/users\/?$/', $request):
            $controller = new ApiController();

            switch($method) {
                case 'POST':
                    $controller->searchUsers($_POST);
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }

            break;

        default:
            $controller = new ErrorPageController();
            $controller->error(404);
    }