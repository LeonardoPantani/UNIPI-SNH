<?php

namespace App\Models;

require_once __DIR__ . '/Novel.php';

use PDO;
use App\Models\Novel;

class NovelText extends Novel {
    private int $form_id;
    private string $form_content;

    public function __construct(?int $id, ?string $uuid, string $title, bool $isPremium, ?string $created_at, int $user_id, ?int $form_id, string $form_content) {
        parent::__construct($id, $uuid, $title, $isPremium, $created_at, $user_id);

        $this->form_id = $form_id;
        $this->form_content = $form_content;
    }

    public function getFormContent(): string
    {
        return $this->form_content;
    }

    public static function addNovelText(string $title, bool $isPremium, string $content, int $user_id, PDO $conn = null) : bool {
        $isLocal = false;

        if(is_null($conn)) {
            $isLocal = true;
            $conn = self::newDBInstance();
        }

        if(!self::db_isTransactionActive($conn)) {
            if(!self::db_transaction($conn)) {
                return false;
            }
        }

        $form_id = self::db_getLastInsertId(
            "INSERT INTO text_form (content) VALUES (?)",
            [$content],
            $conn
        );

        if($form_id < 1) {
            if($isLocal) {
                self::db_rollback($conn);
            }

            return false;
        }

        $res = self::addNovel($title, $isPremium, self::TEXT_FORM, $form_id, $user_id);

        if(!$res) {
            if($isLocal) {
                self::db_rollback($conn);
            }

            return false;
        }

        if($isLocal) {
            self::db_commit($conn);
        }

        return true;
    }
}