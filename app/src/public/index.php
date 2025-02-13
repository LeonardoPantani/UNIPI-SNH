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

    use App\Controllers\HomeController;
    use App\Controllers\LoginController;
    use App\Controllers\UserController;
    use App\Controllers\NovelController;
    use App\Controllers\ForgotPasswordController;
    use App\Controllers\AdminController;
    use App\Controllers\ApiController;
    use App\Controllers\SettingsController;
    use App\Controllers\ErrorPageController;
    
    session_start();

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
            $controller = new UserController($_SERVER, $_GET, $_POST);

            switch($method) {
                case 'GET':
                    $controller->new();
                    break;

                case 'POST':
                    $controller->create();
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }

            break;

        // GET|POST /login
        case (bool) preg_match('/^\/login\/?$/', $request):
            $controller = new LoginController($_SERVER, $_GET, $_POST);

            switch($method) {
                case 'GET':
                    $controller->new();
                    break;

                case 'POST':
                    $controller->login();
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }

            break;

        // GET /logout
        case (bool) preg_match('/^\/logout\/?$/', $request):
            $controller = new LoginController($_SERVER, $_GET, $_POST);

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
            $controller = new SettingsController($_SERVER, $_GET, $_POST);

            switch($method) {
                case 'GET':
                    $controller->new();
                    break;

                case 'POST':
                    $controller->settings_change();
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }

            break;

        // GET|POST /password/reset
        case (bool) preg_match('/^\/password\/reset\/?$/', $request):
            $controller = new ForgotPasswordController($_SERVER, $_GET, $_POST);

            switch($method) {
                case 'GET':
                    $controller->new();
                    break;

                case 'POST':
                    $controller->validate_reset_request();
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }

            break;

        // GET|POST /password/reset/:token
        case (bool) preg_match('/^\/password\/reset\/([0-9a-z]{5})\/?$/', $request, $matches):
            $params = [
                "token" => $matches[1]
            ];

            $controller = new ForgotPasswordController($_SERVER, $_GET, $_POST);

            switch($method) {
                case 'GET':
                    $controller->choose_new_password();
                    break;

                case 'POST':
                    $controller->set_new_password();
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }

            break;

        // GET|POST /novel/add
        case (bool) preg_match('/^\/novel\/add\/?$/', $request):
            $controller = new NovelController($_SERVER, $_GET, $_POST, $_FILES);

            switch($method) {
                case 'GET':
                    $controller->new();
                    break;

                case 'POST':
                    $controller->create();
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }
            
            break;

        // GET /novels
        case (bool) preg_match('/^\/novels\/?$/', $request):
            $controller = new NovelController($_SERVER, $_GET, $_POST, $_FILES);

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
            $params = [
                "uuid" => $matches[1]
            ];

            $controller = new NovelController($_SERVER, array_merge($_GET, $params), $_POST, $_FILES);

            switch($method) {
                case 'GET':
                    $controller->show();
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }
            
            break;

            // GET /user/novels
            case (bool) preg_match('/^\/user\/novels\/?$/', $request):
                $controller = new NovelController($_SERVER, $_GET, $_POST, $_FILES);
    
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
            $controller = new AdminController($_SERVER, $_GET, $_POST);

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
            $controller = new AdminController($_SERVER, $_GET, $_POST);

            switch($method) {
                case 'GET':
                    $controller->edit_user();
                    break;

                case 'POST':
                    $controller->request_user_edit();
                    break;

                default:
                    $controller = new ErrorPageController();
                    $controller->error(405);
            }
            
            break;

        // POST /api/v1/users
        case (bool) preg_match('/^\/api\/v1\/users\/?$/', $request):
            $controller = new ApiController($_SERVER, $_GET, $_POST);

            switch($method) {
                case 'POST':
                    $controller->searchUsers();
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

    /*
    match(true) {
        (bool) preg_match('/\/?/', $request) => (new HomeController())->new(),
        default => http_response_code(404)
    }
    */