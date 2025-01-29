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
    private array $server;
    private array $params;

    public function __construct(array $server, array $params_get, array $params_post) {
        $this->server = $server;

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
                break;

            case 'file':
                // TODO: get and save file
                //$res = NovelFile::addNovelFile($title, $isPremium, $path, $user_id);
                
                break;

            default:
                $logger->info('Unknown "form" parameter');
                $_SESSION['flash']['error'] = 'Invalid type';
                $this->new();

                return;
        }

        if(!$res) {
            $logger->info('Database error during novel creation');
            $_SESSION['flash']['error'] = 'Invalid novel data';
            $this->new();
            return;
        }

        $_SESSION['flash']['success'] = 'Novel created!';
        header("Location: ". "/");
    }
}