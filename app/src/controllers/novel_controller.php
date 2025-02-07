<?php

namespace App\Controllers;

require_once __DIR__ . '/../libs/utils/db/DBConnection.php'; //IMPORTANT - DO NOT DELETE OR CHANGE POSITION
require_once __DIR__ . '/../models/Novel.php';
require_once __DIR__ . '/../models/NovelText.php';
require_once __DIR__ . '/../models/NovelFile.php';
require_once __DIR__ . '/../libs/utils/log/logger.php';
require_once __DIR__ . '/../libs/utils/view/ViewManager.php';

use App\Models\Novel;
use App\Models\NovelText;
use App\Models\NovelFile;
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

    // GET /storyforge/add_novel.php
    function new() {
        $logger = getLogger('add novel');
        $logger->info('GET /storyforge/add_novel.php');

        if(!isset($_SESSION["user"])) {
            $logger->info("User tried to add a novel but is not authenticated");
            $_SESSION['flash']['error'] = 'You are not authenticated.';
            header("Location: ". "login.php");
            
            return;
        }

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        ViewManager::render("add_novel", ["flash" => $flash]);
    }

    // POST /storyforge/add_novel.php
    function create() {
        $logger = getLogger('add novel');
        $logger->info('POST /storyforge/add_novel.php');

        if(!isset($_SESSION["user"])) {
            $logger->info("User tried to add a novel but is not authenticated");
            $_SESSION['flash']['error'] = 'You are not authenticated.';
            header("Location: ". "login.php");

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
        header("Location: ". "/");
    }
}