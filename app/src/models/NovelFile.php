<?php

namespace App\Models;

require_once __DIR__ . '/Novel.php';

use App\Models\Novel;

class NovelFile extends Novel {
    private int $form_id;
    private string $form_path;

    public function __construct(?int $id, ?string $uuid, string $title, bool $isPremium, ?string $created_at, int $user_id, ?int $form_id, string $form_path) {
        parent::__construct($id, $uuid, $title, $isPremium, $created_at, $user_id);

        $this->form_id = $form_id;
        $this->form_path = $form_path;
    }

    public function getFormId() {
        return $this->form_id;
    }

    public function getFormPath() {
        return $this->form_path;
    }

    public static function addNovelFile(string $title, bool $isPremium, string $path, int $user_id) : bool {
        //TODO: add transaction
        
        $form_id = self::db_getLastInsertId(
            "INSERT INTO file_form (path) VALUES (?)",
            $path
        );

        if($form_id < 1) {
            return false;
        }

        return self::addNovel($title, $isPremium, self::FILE_FORM, $form_id, $user_id);
    }
}