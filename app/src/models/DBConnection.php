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

    protected static function db_fetchOne(string $sql, array ... $params) : object {
        $conn = self::db_connect();
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected static function db_fetchAll(string $sql, array ... $params) : array {
        $conn = self::db_connect();
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    protected static function db_getOutcome(string $sql, array ... $params) : bool {
        $conn = self::db_connect();
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute($params);

        return $res;
    }

    private static function db_disconnect(PDO $conn) : void {
        unset($conn);
    }
}