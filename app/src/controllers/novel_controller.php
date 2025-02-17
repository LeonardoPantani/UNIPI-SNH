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
require_once __DIR__ . '/../libs/utils/validator/validator.php';

use App\Models\Novel;
use App\Models\NovelText;
use App\Models\NovelFile;
use App\Models\User;
use App\Utils\ViewManager;
use App\Utils\Validator;

class NovelController {
    private const string UPLOADS_PATH = __DIR__ . '/../uploads/';
    private const array PLACEHOLDERS_TITLE = [
        "The Hunger Games: The Snack Edition",
        "Pride and Prejudice... and the Spilled Coffee",
        "Moby-Dick and the Whale That Got Away",
        "Harry Potter and the Quest for the Perfect Burrito",
        "1984: The Year of the Selfie Stick",
        "Romeo and Juliet... and the Broken Heart Emoji",
        "I don't need therapy, I just need a Snorlax nap",
        "Sherlock Holmes and the Case of the Missing Socks",
        "Alice's Big Adventure: The Lost Keys and the Mad Tea Party",
        "Treasure Island: Pirates, Plunder, and Really Bad Maps",
        "Zeno's Conscience: A Man and His Endless Regrets",
        "The Decameron: Ten Days of Laughter and Stories to Tell",
    ];
    private const array PLACEHOLDERS_CONTENT = [
        "It was a dark and stormy night...",
        "Midway upon the journey of our life, I found myself within a forest dark...",
        "To be, or not to be...",
        "Abandon all hope, ye who enter...",
        "I have a dream that one day...",
        "All that we see or seem is but a dream...",
        "The only thing we have to fear is...",
        "Elementary, my dear...",
        "The first rule of Fight Club is...",
        "That's one small step for man, one giant...",
        "War is peace. Freedom is slavery. Ignorance is...",
        "Houston, we have a...",
        "The answer to the ultimate question of life, the universe, and everything is...",
        "Winter is coming... when the snows fall and the white winds blow...",
        "The reports of my death are greatly...",
    ];

    // GET /novel/add
    public function new(): void
    {
        $logger = getLogger('add novel');
        $logger->info('GET /novel/add');

        if(!isset($_SESSION["user"])) {
            $logger->info("User tried to add a novel but is not authenticated");
            $_SESSION['flash']['error'] = 'You are not authenticated.';
            header('Location: '. LOGIN_PATH);
            
            return;
        }

        $token = $_SESSION["token"];
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("add_novel", ["flash" => $flash, "token" => $token, "title_maxlength" => Validator::NOVEL_TITLE_MAX_LENGTH, "content_maxlength" => Validator::NOVEL_TEXT_MAX_LENGTH, "title_placeholder" => self::PLACEHOLDERS_TITLE[array_rand(self::PLACEHOLDERS_TITLE)], "content_placeholder" => self::PLACEHOLDERS_CONTENT[array_rand(self::PLACEHOLDERS_CONTENT)]]);
    }

    // POST /novel/add
    public function create($params_post, $params_file): void
    {
        $logger = getLogger('add novel');
        $logger->info('POST /novel/add');

        if(!isset($_SESSION["user"])) {
            $logger->info("User tried to add a novel but is not authenticated");
            $_SESSION['flash']['error'] = 'You are not authenticated.';
            header('Location: ' . LOGIN_PATH);

            return;
        }

        if(!isset($params_post["token"]) || $params_post["token"] !== $_SESSION["token"]) {
            $logger->info('Invalid CSRF token');
            $_SESSION['flash']['error'] = 'Invalid CSRF token';
            $this->new();
            return;
        }

        if(!isset($params_post['title']) || strlen($params_post['title']) <= 0) {
            $logger->info('Invalid novel title');
            $_SESSION['flash']['error'] = 'Invalid novel title';
            $this->new();

            return;
        }

        if(!isset($params_post['premium']) || !in_array($params_post['premium'], ["0", "1"])) {
            $logger->info('Invalid novel premium');
            $_SESSION['flash']['error'] = 'Invalid novel premium';
            $this->new();

            return;
        }

        if(!isset($params_post['novel_form'])) {
            $logger->info('Missing novel form');
            $_SESSION['flash']['error'] = 'Missing novel form';
            $this->new();

            return;
        }

        $title     = $params_post['title'];
        $form      = $params_post['novel_form'];
        $isPremium = ((int) $params_post['premium']) > 0;
        $user_id   = $_SESSION['user'];

        if(Novel::titleAndUserExists($title, $user_id)) {
            $logger->info('User cannot create more than one novel with the same title');
            $_SESSION['flash']['error'] = 'You cannot create more than one novel with the same title';
            $this->new();

            return;
        }

        switch($form) {
            case 'text':
                if(!isset($params_post['content']) || strlen($params_post['content']) <= 0 || strlen($params_post['content']) > 500) {
                    $logger->info('Invalid novel content');
                    $_SESSION['flash']['error'] = 'Invalid novel content - max 500 characters';
                    $this->new();

                    return;
                }

                $content = $params_post['content'];
                $res = NovelText::addNovelText($title, $isPremium, $content, $user_id);

                if(!$res) {
                    $logger->info('Database error during novel creation');
                    $_SESSION['flash']['error'] = 'Invalid novel data';
                    $this->new();

                    return;
                }

                break;

            case 'file':

                if(!isset($params_file['file'])) {
                    $logger->info('Missing novel file');
                    $_SESSION['flash']['error'] = 'Missing novel file';
                    $this->new();
        
                    return;
                }

                $file = $params_file['file'];
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
    function showAll(): void
    {
        $logger = getLogger('show novels');
        $logger->info('GET /novels');

        if(!isset($_SESSION["user"])) {
            $logger->info("User tried to access to the novels page but is not authenticated");
            $_SESSION['flash']['error'] = 'You are not authenticated.';
            header('Location: ' . LOGIN_PATH);

            return;
        }

        $user = User::getUserById($_SESSION['user']);
        $novels = ($user->getRoleName() === 'nonpremium')
            ? Novel::getAllNonPremiumNovels()
            : Novel::getAllNovels();

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
                    $novels_text[] = $item;
                    break;
    
                case 'App\Models\NovelFile':
                    $novels_file[] = $item;
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
    function show($params_path): void
    {
        $logger = getLogger('show a novel');
        $logger->info('GET /novels/:uuid');

        if(!isset($_SESSION["user"])) {
            $logger->info("User tried to access to a novel page but is not authenticated");
            $_SESSION['flash']['error'] = 'You are not authenticated.';
            header('Location: ' . LOGIN_PATH);

            return;
        }

        if(!isset($params_path['uuid']) || !Validator::uuidValidation($params_path['uuid'])) {
            $logger->info("Invalid novel uuid");
            $_SESSION['flash']['error'] = 'Invalid novel uuid';
            header('Location: ' . ROOT_PATH);

            return;
        }

        $uuid    = $params_path['uuid'];
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
        if($novel->getUserId() !== $user_id && ($novel->getIsPremium() && $user->getRoleName() === 'nonpremium')) {
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

                ViewManager::renderPdf($path);

                break;

            default:
                $logger->info("unknown novel form");
                $_SESSION['flash']['error'] = 'Nove not found';
                header('Location: ' . SHOW_NOVELS_PATH);
        }
    }

    // GET /user/novels
    function showUser(): void
    {
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
                    $novels_text[] = $item;
                    break;
    
                case 'App\Models\NovelFile':
                    $novels_file[] = $item;
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