<?php

namespace App\Models;

require_once __DIR__ . '/Novel.php';

use App\Models\Novel;

class NovelText extends Novel {
    private int $form_id;
    private string $form_content;

    public function __construct(?int $id, ?string $uuid, string $title, bool $isPremium, ?string $created_at, int $user_id, ?int $form_id, string $form_content) {
        parent::__construct($id, $uuid, $title, $isPremium, $created_at, $user_id);

        $this->form_id = $form_id;
        $this->form_content = $form_content;
    }

    public function getFormId() {
        return $this->form_id;
    }

    public function getFormContent() {
        return $this->form_content;
    }

    public static function addNovelText(string $title, bool $isPremium, string $content, int $user_id) : bool {
        //TODO: add transaction

        $form_id = self::db_getLastInsertId(
            "INSERT INTO text_form (content) VALUES (?)",
            [$content]
        );

        if($form_id < 1) {
            return false;
        }

        return self::addNovel($title, $isPremium, self::TEXT_FORM, $form_id, $user_id);
    }
}