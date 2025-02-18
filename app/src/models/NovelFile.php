<?php

namespace App\Models;

require_once __DIR__ . '/Novel.php';

use PDO;

class NovelFile extends Novel {
    private int $form_id;
    private string $form_path;

    public function __construct(?int $id, ?string $uuid, string $title, bool $isPremium, ?string $created_at, int $user_id, ?int $form_id, string $form_path) {
        parent::__construct($id, $uuid, $title, $isPremium, $created_at, $user_id);

        $this->form_id = $form_id;
        $this->form_path = $form_path;
    }

    public function getFormId(): ?int
    {
        return $this->form_id;
    }

    public function getFormPath(): string
    {
        return $this->form_path;
    }

    public static function addNovelFile(string $title, bool $isPremium, string $path, int $user_id, PDO $conn = null) : bool {
        $isLocal = false;

        if(is_null($conn)) {
            $isLocal = true;
            $conn = self::newDBInstance();

            if(is_null($conn)) {
                return false;
            }
        }

        if(!self::db_isTransactionActive($conn)) {
            if(!self::db_transaction($conn)) {
                return false;
            }
        }

        $form_id = self::db_getLastInsertId(
            "INSERT INTO file_form (path) VALUES (?)",
            [$path],
            $conn
        );

        if($form_id < 1) {
            if($isLocal) {
                self::db_rollback($conn);
            }

            return false;
        }

        $res = self::addNovel($title, $isPremium, self::FILE_FORM, $form_id, $user_id);

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