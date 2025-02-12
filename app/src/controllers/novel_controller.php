<?php

namespace App\Controllers;

require_once __DIR__ . '/../libs/utils/db/DBConnection.php'; //IMPORTANT - DO NOT DELETE OR CHANGE POSITION
require_once __DIR__ . '/../models/Novel.php';
require_once __DIR__ . '/../models/NovelText.php';
require_once __DIR__ . '/../models/NovelFile.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../libs/utils/view/ViewManager.php';
require_once __DIR__ . '/../libs/utils/config/constants.php';

use App\Models\Novel;
use App\Models\NovelText;
use App\Models\NovelFile;
use App\Models\User;
use App\Utils\ViewManager;

class NovelController {
    const string UPLOADS_PATH = __DIR__ . '/../uploads/';

    private array $server;
    private array $params;
    private array $files;

    public function __construct(array $server, array $params_get, array $params_post, array $files) {
        $this->server = $server;
        $this->files = $files;

        $this->params = array(
            'GET'  => $params_get,
            'POST' => $params_post
        );
    }

    // GET /novel/add
    function new() {
        $logger = getLogger('add novel');
        $logger->info('GET /novel/add');

        if(!isset($_SESSION["user"])) {
            $logger->info("User tried to add a novel but is not authenticated");
            $_SESSION['flash']['error'] = 'You are not authenticated.';
            header('Location: '. LOGIN_PATH);
            
            return;
        }

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("add_novel", ["flash" => $flash]);
    }

    // POST /novel/add
    function create() {
        $logger = getLogger('add novel');
        $logger->info('POST /novel/add');

        if(!isset($_SESSION["user"])) {
            $logger->info("User tried to add a novel but is not authenticated");
            $_SESSION['flash']['error'] = 'You are not authenticated.';
            header('Location: ' . LOGIN_PATH);

            return;
        }

        $title = $this->params['POST']['title'];
        $form  = $this->params['POST']['novel_form'];
        $isPremium = ((int) $this->params['POST']['premium']) > 0;
        $user_id = $_SESSION['user'];

        // TODO: Validator

        switch($form) {
            case 'text':
                $content = $this->params['POST']['content'];
                $res = NovelText::addNovelText($title, $isPremium, $content, $user_id);

                if(!$res) {
                    $logger->info('Database error during novel creation');
                    $_SESSION['flash']['error'] = 'Invalid novel data';
                    $this->new();

                    return;
                }

                break;

            case 'file':
                $file = $this->files['file'];
                $tmp_filename = $file['tmp_name'];

                // check mime type
                $mime = mime_content_type($tmp_filename);

                if($file['type'] !== 'application/pdf' || $mime !== 'application/pdf') {
                    $logger->info('invalid file type');
                    $_SESSION['flash']['error'] = 'Invalid file type';
                    $this->new();

                    return;
                }

                // check if file exists
                $i = 0;
                $is_valid = false;

                while($i < 3) {
                    $random_string = rtrim(strtr(base64_encode(random_bytes(25)), '/', '_'), '=');
                    $filename = realpath(self::UPLOADS_PATH) . '/' . $random_string . '.pdf';

                    if(!$filename) {
                        $logger->info('realpath failure');
                        $_SESSION['flash']['error'] = 'Internal server error';
                        $this->new();
    
                        return;
                    }

                    if(!file_exists($filename)) {
                        $is_valid = true;
                        break;
                    }

                    $i++;
                }

                if(!$is_valid) {
                    $logger->info('cannot create a valid random string for file name');
                    $_SESSION['flash']['error'] = 'Internal server error';
                    $this->new();

                    return;
                }
                
                // check file size
                $max_size = 50 * 1000; //50Kb
                if ($file["size"] > $max_size) {
                    $logger->info('file too large');
                    $_SESSION['flash']['error'] = 'File is too large (max 50Kb)';
                    $this->new();

                    return;
                }

                // new db instance
                $conn = NovelFile::newDBInstance();

                // create transaction
                if(!NovelFile::db_transaction($conn)) {
                    $logger->info('cannot create a db transaction');
                    $_SESSION['flash']['error'] = 'Internal server error';
                    $this->new();

                    return;
                }

                $res = NovelFile::addNovelFile($title, $isPremium, $filename, $user_id, $conn);

                if(!$res) {
                    NovelFile::db_rollback($conn);

                    $logger->info('Database error during novel creation');
                    $_SESSION['flash']['error'] = 'Invalid novel data';
                    $this->new();

                    return;
                }

                // move file from /tmp to app/uploads
                if(!move_uploaded_file($tmp_filename, $filename)) {
                    NovelFile::db_rollback($conn);

                    $logger->info('cannot save file');
                    $_SESSION['flash']['error'] = 'Invalid file';
                    $this->new();

                    return;
                }
                
                // commit db
                NovelFile::db_commit($conn);

                break;

            default:
                $logger->info('Unknown "form" parameter');
                $_SESSION['flash']['error'] = 'Invalid type';
                $this->new();

                return;
        }

        $_SESSION['flash']['success'] = 'Novel created!';
        header('Location: ' . SHOW_USER_NOVELS_PATH);
    }

    // GET /novels
    function showAll() {
        $logger = getLogger('show novels');
        $logger->info('GET /novels');

        if(!isset($_SESSION["user"])) {
            $logger->info("User tried to access to the novels page but is not authenticated");
            $_SESSION['flash']['error'] = 'You are not authenticated.';
            header('Location: ' . LOGIN_PATH);

            return;
        }

        $user = User::getUserById($_SESSION['user']);
        $novels = ($user->getRoleName() === 'premium')
            ? Novel::getAllNovels() 
            : Novel::getAllNonPremiumNovels();

        $novels_text = array();
        $novels_file = array();
        foreach($novels as $novel) {
            $item = [
                "title"     => $novel->getTitle(),
                "isPremium" => $novel->getIsPremium(),
                "url"       => show_novel_path($novel->getUuid())
            ];
            
            switch(get_class($novel)) {
                case 'App\Models\NovelText':
                    array_push($novels_text, $item);
                    break;
    
                case 'App\Models\NovelFile':
                    array_push($novels_file, $item);
                    break;

                default:
                    $logger->info("unknown novel form");
                    $_SESSION['flash']['error'] = 'Internal server error';
                    header('Location: ' . ROOT_PATH);
    
                    return;
            }
        }

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("show_all_novels", ["flash" => $flash, "novels_text" => $novels_text, "novels_file" => $novels_file]);
    }

    // GET /novels/:uuid
    function show() {
        $logger = getLogger('show a novel');
        $logger->info('GET /novels/:uuid');

        if(!isset($_SESSION["user"])) {
            $logger->info("User tried to access to a novel page but is not authenticated");
            $_SESSION['flash']['error'] = 'You are not authenticated.';
            header('Location: ' . LOGIN_PATH);

            return;
        }

        $uuid = $this->params['GET']['uuid'];
        $user_id = $_SESSION['user'];

        $user = User::getUserById($user_id);
        $novel = Novel::getNovelByUuid($uuid);

        if(is_null($novel)) {
            $logger->info("novel not found");
            $_SESSION['flash']['error'] = 'Novel not found';
            header('Location: ' . SHOW_NOVELS_PATH);

            return;
        }

        // check premium permission only if the novel doesn't belong to the current user
        if($novel->getUserId() !== $user_id && ($novel->getIsPremium() && $user->getRoleName() !== 'premium')) {
            $logger->info("User tried to access a premium novel without permissions");
            $_SESSION['flash']['error'] = 'You \'re not allowed to see premium novels';
            header('Location: ' . SHOW_NOVELS_PATH);

            return;
        }

        $novel_user = User::getUserById($novel->getUserId());

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        switch(get_class($novel)) {
            case 'App\Models\NovelText':
                ViewManager::render('show_text_novel', [
                    "flash"      => $flash, 
                    "novel_user" => ["username" => $novel_user->getUsername()],
                    "novel"      => ["title" => $novel->getTitle(), "formContent" => $novel->getFormContent()]
                ]);

                break;

            case 'App\Models\NovelFile':
                $path = $novel->getFormPath();

                if(!file_exists($path)) {
                    $logger->info("file $path not found");
                    $_SESSION['flash']['error'] = 'Novel PDF not found';
                    header('Location: ' . SHOW_NOVELS_PATH);

                    return;
                }

                header('Content-Type: application/pdf');
                readfile($path);

                break;

            default:
                $logger->info("unknown novel form");
                $_SESSION['flash']['error'] = 'Nove not found';
                header('Location: ' . SHOW_NOVELS_PATH);

                return;
        }
    }

    // GET /user/novels
    function showUser() {
        $logger = getLogger('show user novels');
        $logger->info('GET /user/novels');

        if(!isset($_SESSION["user"])) {
            $logger->info("User tried to access to his novels page but is not authenticated");
            $_SESSION['flash']['error'] = 'You are not authenticated.';
            header('Location: ' . LOGIN_PATH);

            return;
        }

        $user_id = $_SESSION['user'];
        $novels = Novel::getAllNovelsByUserId($user_id); 

        $novels_text = array();
        $novels_file = array();
        foreach($novels as $novel) {
            $item = [
                "title"     => $novel->getTitle(),
                "isPremium" => $novel->getIsPremium(),
                "url"       => show_novel_path($novel->getUuid())
            ];
            
            switch(get_class($novel)) {
                case 'App\Models\NovelText':
                    array_push($novels_text, $item);
                    break;
    
                case 'App\Models\NovelFile':
                    array_push($novels_file, $item);
                    break;

                default:
                    $logger->info("unknown novel form");
                    $_SESSION['flash']['error'] = 'Internal server error';
                    header('Location: ' . ROOT_PATH);
    
                    return;
            }
        }

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("show_user_novels", ["flash" => $flash, "novels_text" => $novels_text, "novels_file" => $novels_file]);
    }
}