<?php

namespace App\Models;

use PDO;
use PDOException;
use Exception;

abstract class DBConnection {
    private static function db_connect($host = null, $user = null, $psw = null, $name = null) : PDO {
        $host = $host ?? $_ENV["DB_HOST"];
        $user = $user ?? $_ENV["DB_USER"];
        $psw = $psw ?? $_ENV["DB_PASSWORD"];
        $name = $name ?? $_ENV["DB_NAME"];

        $dsn = "mysql:host=$host;dbname=$name;charset=utf8";

        try {
            $conn = new PDO($dsn, $user, $psw, array(PDO::ATTR_TIMEOUT => 1, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }

        return $conn;
    }

    protected static function db_fetchOne(string $sql, string ... $params) : array {
        $conn = self::db_connect();
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    protected static function db_fetchAll(string $sql, string ... $params) : array {
        $conn = self::db_connect();
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    protected static function db_getOutcome(string $sql, string ... $params) : bool {
        $conn = self::db_connect();
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute($params);

        return $res;
    }

    protected static function db_numRows(string $sql, string ... $params) : int {
        $conn = self::db_connect();
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute($params);

        return count($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []);
    }

    protected static function db_contains(string $sql, string ... $params) : bool {
        $conn = self::db_connect();
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute($params);

        return ((int) $stmt->fetchColumn()) > 0;
    }

    private static function db_disconnect(PDO $conn) : void {
        unset($conn);
    }
}