<?php

namespace App\Models;

require_once __DIR__ . '/../models/DBConnection.php';

use App\Models\DBConnection;

class User extends DBConnection
{
    private $conn;

    private string $uuid;
    private string $email;
    private string $username;
    private string $password;
    private string $created_at;
    private int $role_id;

    public function getUuid(): string {
        return $this->uuid;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getCreatedAt(): string {
        return $this->created_at;
    }

    public function getRoleId(): int {
        return $this->role_id;
    }

    private function __construct(?string $uuid, string $email, string $username, string $password, ?string $created_at, int $role_id) {
        $this->uuid = $uuid;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->created_at = $created_at;
        $this->role_id = $role_id;
    }

    public static function newUserInstance($email, $username, $password) : User {
        return new User(null, $email, $username, $password, null, self::getRoleByName("nonpremium"));
    }

    private static function getRoleByName($role_name) : int {
        return self::db_fetchOne("SELECT id FROM role WHERE name = ?", $role_name)["role_id"];
    }

    public static function addUser($email, $username, $password) : bool {
        return self::db_getOutcome(
            "INSERT INTO users (UUID(), email, username, password_hash, NOW(), role_id) VALUES (?, ?, ?, ?)",
            $email, $username, password_hash($password, PASSWORD_DEFAULT)
        );
    }

    public static function getAllUsers(): array {
        $res = self::db_fetchAll("SELECT * FROM users");
    
        return array_map(fn($row) => new User(
            $row['uuid'],
            $row['email'],
            $row['username'],
            $row['password_hash'],
            $row['created_at'],
            $row['role_id']
        ), $res);
    }
}