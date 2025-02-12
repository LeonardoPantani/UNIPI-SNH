<?php

namespace App\Models;

require_once __DIR__ . '/NovelText.php';
require_once __DIR__ . '/NovelFile.php';
require_once __DIR__ . '/../libs/utils/db/DBConnection.php';

use App\Models\NovelText;
use App\Models\NovelFile;
use App\Utils\DBConnection;

abstract class Novel extends DBConnection {
    protected const string TEXT_FORM = 'text_form';
    protected const string FILE_FORM = 'file_form';

    private ?int $id;
    private ?string $uuid;
    private string $title;
    private bool $isPremium;
    /*
    private int $form_type;
    private int $form_id;
    */
    private ?string $created_at;
    private int $user_id;

    protected function __construct(?int $id, ?string $uuid, string $title, bool $isPremium, ?string $created_at, int $user_id) {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->title = $title;
        $this->isPremium = $isPremium;
        /*
        $this->form_type = $form_type;
        $this->form_id = $form_id;
        */
        $this->created_at = $created_at;
        $this->user_id = $user_id;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getUuid(): string {
        return $this->uuid;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getIsPremium(): bool {
        return $this->isPremium;
    }

    /*
    public function getFormType(): int {
        return $this->form_type;
    }

    public function getFormId(): int {
        return $this->form_id;
    }
    */

    public function getCreatedAt(): string {
        return $this->created_at;
    }

    public function getUserId(): int {
        return $this->user_id;
    }

    protected static function addNovel(string $title, bool $isPremium, string $form_type, int $form_id, int $user_id, PDO $conn = null): bool {
        $premium = ($isPremium) ? 1 : 0;

        return self::db_getOutcome(
            "INSERT INTO novel (uuid, title, premium, form_type, form_id, created_at, user_id) VALUES (UUID(), ?, ?, ?, ?, NOW(), ?)",
            [$title, $premium, $form_type, $form_id, $user_id],
            $conn
        );
    }

    public static function getNovelByUuid(string $uuid) : ?Novel {
        $novel_row = self::db_fetchOne(
            "SELECT * FROM novel WHERE uuid = ?",
            [$uuid]
        );

        if(count($novel_row) <= 0) {
            return null;
        }

        $novel_row['premium'] = ((int) $novel_row['premium']) > 0;

        switch ($novel_row['form_type']) {
            case (string) self::TEXT_FORM:
                $form_row = self::db_fetchOne(
                    "SELECT id, content FROM text_form WHERE id = ?",
                    [$novel_row['form_id']]
                );

                $novel = new NovelText(
                    $novel_row['id'], $novel_row['uuid'], $novel_row['title'], $novel_row['premium'], $novel_row['created_at'], $novel_row['user_id'], 
                    $form_row['id'], $form_row['content']
                );

                break;

            case (string) self::FILE_FORM:
                $form_row = self::db_fetchOne(
                    "SELECT id, path FROM file_form WHERE id = ?",
                    [$novel_row['form_id']]
                );

                $novel = new NovelFile(
                    $novel_row['id'], $novel_row['uuid'], $novel_row['title'], $novel_row['premium'], $novel_row['created_at'], $novel_row['user_id'], 
                    $form_row['id'], $form_row['path']
                );

                break;

            default:
                $novel = null;
        }

        return $novel;
    }

    public static function getAllNovels() : array {
        $novels_text = self::db_fetchAll("
            SELECT n.id, n.uuid, n.title, n.premium, n.created_at, n.user_id, t.id as form_id, t.content as form_content 
            FROM novel n 
            INNER JOIN text_form t ON (n.form_type = ? AND n.form_id = t.id)
        ", [self::TEXT_FORM]);
        
        $novels_text = array_map(fn($row) => new NovelText(
            (int) $row['id'],
            $row['uuid'],
            $row['title'],
            ((int) $row['premium']) > 0,
            $row['created_at'],
            $row['user_id'],
            $row['form_id'],
            $row['form_content']
        ), $novels_text);

        $novels_file = self::db_fetchAll("
            SELECT n.id, n.uuid, n.title, n.premium, n.created_at, n.user_id, t.id as form_id, t.path as form_path 
            FROM novel n 
            INNER JOIN file_form t ON (n.form_type = ? AND n.form_id = t.id)
        ", [self::FILE_FORM]);

        $novels_file = array_map(fn($row) => new NovelFile(
            (int) $row['id'],
            $row['uuid'],
            $row['title'],
            ((int) $row['premium']) > 0,
            $row['created_at'],
            $row['user_id'],
            $row['form_id'],
            $row['form_path']
        ), $novels_file);

        return array_merge($novels_text, $novels_file);
    }

    public static function getAllNonPremiumNovels() : array {
        $novels_text = self::db_fetchAll("
            SELECT n.id, n.uuid, n.title, n.premium, n.created_at, n.user_id, t.id as form_id, t.content as form_content 
            FROM novel n 
            INNER JOIN text_form t ON (n.form_type = ? AND n.form_id = t.id)
            WHERE n.premium = 0
        ", [self::TEXT_FORM]);
        
        $novels_text = array_map(fn($row) => new NovelText(
            (int) $row['id'],
            $row['uuid'],
            $row['title'],
            ((int) $row['premium']) > 0,
            $row['created_at'],
            $row['user_id'],
            $row['form_id'],
            $row['form_content']
        ), $novels_text);

        $novels_file = self::db_fetchAll("
            SELECT n.id, n.uuid, n.title, n.premium, n.created_at, n.user_id, t.id as form_id, t.path as form_path 
            FROM novel n 
            INNER JOIN file_form t ON (n.form_type = ? AND n.form_id = t.id)
            WHERE n.premium = 0
        ", [self::FILE_FORM]);

        $novels_file = array_map(fn($row) => new NovelFile(
            (int) $row['id'],
            $row['uuid'],
            $row['title'],
            ((int) $row['premium']) > 0,
            $row['created_at'],
            $row['user_id'],
            $row['form_id'],
            $row['form_path']
        ), $novels_file);

        return array_merge($novels_text, $novels_file);
    }

    public static function getAllNovelsByUserId(int $user_id) : array {
        $novels_text = self::db_fetchAll("
            SELECT n.id, n.uuid, n.title, n.premium, n.created_at, n.user_id, t.id as form_id, t.content as form_content 
            FROM novel n 
            INNER JOIN text_form t ON (n.form_type = ? AND n.form_id = t.id)
            WHERE n.user_id = ?
        ", [self::TEXT_FORM, $user_id]);
        
        $novels_text = array_map(fn($row) => new NovelText(
            (int) $row['id'],
            $row['uuid'],
            $row['title'],
            ((int) $row['premium']) > 0,
            $row['created_at'],
            $row['user_id'],
            $row['form_id'],
            $row['form_content']
        ), $novels_text);

        $novels_file = self::db_fetchAll("
            SELECT n.id, n.uuid, n.title, n.premium, n.created_at, n.user_id, t.id as form_id, t.path as form_path 
            FROM novel n 
            INNER JOIN file_form t ON (n.form_type = ? AND n.form_id = t.id)
            WHERE n.user_id = ?
        ", [self::FILE_FORM,  $user_id]);

        $novels_file = array_map(fn($row) => new NovelFile(
            (int) $row['id'],
            $row['uuid'],
            $row['title'],
            ((int) $row['premium']) > 0,
            $row['created_at'],
            $row['user_id'],
            $row['form_id'],
            $row['form_path']
        ), $novels_file);

        return array_merge($novels_text, $novels_file);
    }
}