<?php

namespace App\Utils;

use PDO;
use PDOException;
use Exception;

abstract class DBConnection {
    private static function db_connect($host = null, $user = null, $psw = null, $name = null) : PDO {
        $host = $host ?? $_ENV["DB_HOST"];
        $user = $user ?? $_ENV["DB_USER"];
        $psw = $psw ?? $_ENV["DB_PASSWORD"];
        $name = $name ?? $_ENV["DB_NAME"];

        // NOTE: PHP uses 'latin1' charset, whereas MySQL uses 'utf8' or 'utf8mb4' charset.
        $dsn = "mysql:host=$host;dbname=$name;charset=latin1";

        try {
            $conn = new PDO($dsn, $user, $psw, array(PDO::ATTR_TIMEOUT => 1, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }

        return $conn;
    }

    public static function newDBInstance() : PDO {
        return self::db_connect();
    }

    public static function db_transaction(PDO $conn) : bool {
        try {
            return $conn->beginTransaction();
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function db_isTransactionActive(PDO $conn) : bool {
        return $conn->inTransaction();
    }

    public static function db_commit(PDO $conn) : bool {
        return $conn->commit();
    }

    public static function db_rollback(PDO $conn) : bool {
        return $conn->rollBack();
    }

    protected static function db_fetchOne(string $sql, ?array $params, PDO $conn = null) : array {
        if(is_null($conn)) {
            $conn = self::db_connect();
        }

        $stmt = $conn->prepare($sql);
        $res = $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    protected static function db_fetchAll(string $sql, ?array $params, PDO $conn = null) : array {
        if(is_null($conn)) {
            $conn = self::db_connect();
        }
        
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    protected static function db_getOutcome(string $sql, ?array $params, PDO $conn = null) : bool {
        if(is_null($conn)) {
            $conn = self::db_connect();
        }
        
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute($params);

        return $res;
    }

    protected static function db_getLastInsertId(string $sql, ?array $params, PDO $conn = null) : int {
        if(is_null($conn)) {
            $conn = self::db_connect();
        }

        $stmt = $conn->prepare($sql);
        $res = $stmt->execute($params);

        if(!$res) {
            return -1;
        }

        return $conn->lastInsertId();
    }

    protected static function db_numRows(string $sql, ?array $params, PDO $conn = null) : int {
        if(is_null($conn)) {
            $conn = self::db_connect();
        }

        $stmt = $conn->prepare($sql);
        $res = $stmt->execute($params);

        return count($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []);
    }

    protected static function db_contains(string $sql, ?array $params, PDO $conn = null) : bool {
        if(is_null($conn)) {
            $conn = self::db_connect();
        }

        $stmt = $conn->prepare($sql);
        $res = $stmt->execute($params);

        return ((int) $stmt->fetchColumn()) > 0;
    }

    private static function db_disconnect(PDO $conn) : void {
        unset($conn);
    }
}