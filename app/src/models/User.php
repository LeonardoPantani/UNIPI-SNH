<?php

namespace App\Models;

require_once __DIR__ . '/../libs/utils/db/DBConnection.php';

use App\Utils\DBConnection;

class User extends DBConnection {
    private int $id;
    private ?string $uuid;
    private string $email;
    private string $username;
    private string $password_hash;
    private ?string $created_at;
    private int $role_id;

    public function getId(): int {
        return $this->id;
    }

    public function getUuid(): string {
        return $this->uuid;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getPasswordHash(): string {
        return $this->password_hash;
    }

    public function getCreatedAt(): string {
        return $this->created_at;
    }

    public function getRoleId(): int {
        return $this->role_id;
    }

    public function getRoleName() : string {
        return self::getRoleNameById($this->role_id);
    }

    private function __construct(?int $id, ?string $uuid, string $email, string $username, string $password_hash, ?string $created_at, int $role_id) {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->email = $email;
        $this->username = $username;
        $this->password_hash = $password_hash;
        $this->created_at = $created_at;
        $this->role_id = $role_id;
    }

    public static function newUserInstance(string $email, string $username, string $password) : User {
        return new User(null, null, $email, $username, $password, null, self::getRoleByName("nonpremium"));
    }

    public static function getRoles() : array {
        return self::db_fetchAll("SELECT * FROM role", null) ? : [];
    }

    public static function getNonAdminRoles() : array {
        return self::db_fetchAll("SELECT * FROM role WHERE name != 'admin'", null) ? : [];
    }

    private static function getRoleNameById(int $role_id) : string {
        return self::db_fetchOne(
            "SELECT name FROM role WHERE id = ?", 
            [$role_id]
        )["name"];
    }

    private static function getRoleByName(string $role_name) : int {
        return self::db_fetchOne(
            "SELECT id FROM role WHERE name = ?", 
            [$role_name]
        )["id"];
    }

    public static function usernameExists(string $username): bool {
        return self::db_contains(
            "SELECT id FROM users WHERE username = ?", 
            [$username]
        );
    }

    public static function emailExists(string $email) : bool {
        return self::db_contains(
            "SELECT id FROM users WHERE email = ?", 
            [$email]
        );
    }

    public static function getUserByUsername(string $username) : ?User {
        $row = self::db_fetchOne(
            "SELECT * FROM users WHERE username = ?",
            [$username]
        );

        if(count($row) <= 0) {
            return null;
        }

        return new User(
            (int) $row['id'],
            $row['uuid'],
            $row['email'],
            $row['username'],
            $row['password_hash'],
            $row['created_at'],
            $row['role_id']
        );
    }   
    
    public static function getUserById(int $id) : ?User {
        $row = self::db_fetchOne(
            "SELECT * FROM users WHERE id = ?",
            [$id]
        );

        if(count($row) <= 0) {
            return null;
        }

        return new User(
            (int) $row['id'],
            $row['uuid'],
            $row['email'],
            $row['username'],
            $row['password_hash'],
            $row['created_at'],
            $row['role_id']
        );
    }

    public static function getNonAdminUsersByPartialUsername($partial_username) : array {
        /*
            NOTE: In Mysql, 0 is FALSE and anything else is TRUE. LOCATE(substring, string) returns the position of 
            the first occurrence of a substring in a string (the position is always greater or equal than 1 since 1 
            is the first index of a string). If there is no match, 0 is returned.
            So: 
                match    => TRUE 
                no match => FALSE 
        */
        $res = self::db_fetchAll(
            "SELECT * FROM users u INNER JOIN role r ON u.role_id = r.id WHERE LOCATE(?, username) AND r.name != 'admin'", 
            [$partial_username]
        );   
    
        return array_map(fn($row) => new User(
            (int) $row['id'],
            $row['uuid'],
            $row['email'],
            $row['username'],
            $row['password_hash'],
            $row['created_at'],
            $row['role_id']
        ), $res);
    }

    public static function addUser(string $email, string $username, string $password_hash) : bool {
        return self::db_getOutcome(
            "INSERT INTO users (uuid, email, username, password_hash, created_at, role_id) VALUES (UUID(), ?, ?, ?, NOW(), ?)",
            [$email, $username, $password_hash, self::getRoleByName("nonpremium")]
        );
    }

    public static function updateUserPassword(int $user_id, string $new_password_hash) : bool {
        return self::db_getOutcome(
            "UPDATE users SET password_hash = ? WHERE id = ?", 
            [$new_password_hash, $user_id]
        );
    }

    public static function getAllUsers(): array {
        $res = self::db_fetchAll("SELECT * FROM users", []);
    
        return array_map(fn($row) => new User(
            (int) $row['id'],
            $row['uuid'],
            $row['email'],
            $row['username'],
            $row['password_hash'],
            $row['created_at'],
            $row['role_id']
        ), $res);
    }
}