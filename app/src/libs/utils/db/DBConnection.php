<?php

namespace App\Utils;

require_once __DIR__ . '/../log/logger.php';

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
        } catch (PDOException) {
            return false;
        }
    }

    public static function db_isTransactionActive(PDO $conn) : bool {
        return $conn->inTransaction();
    }

    public static function db_commit(PDO $conn) : bool {
        try {
            $res = $conn->commit();
        } catch(PDOException) {
            return false;
        }
        return $res;
    }

    public static function db_rollback(PDO $conn) : bool {
        try {
            $res = $conn->rollBack();
        } catch(PDOException) {
            return false;
        }
        return $res;
    }

    protected static function db_fetchOne(string $sql, ?array $params, PDO $conn = null) : array {
        try {
            if(is_null($conn)) {
                $conn = self::db_connect();
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException) {
            return [];
        }

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    protected static function db_fetchAll(string $sql, ?array $params, PDO $conn = null) : array {
        try {
            if(is_null($conn)) {
                $conn = self::db_connect();
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException) {
            return [];
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    protected static function db_getOutcome(string $sql, ?array $params, PDO $conn = null) : bool {
        try {
            if (is_null($conn)) {
                $conn = self::db_connect();
            }

            $stmt = $conn->prepare($sql);
            $res = $stmt->execute($params);
        } catch (PDOException) {
            return false;
        }

        return $res;
    }

    protected static function db_getLastInsertId(string $sql, ?array $params, PDO $conn = null) : int {
        try {
            if (is_null($conn)) {
                $conn = self::db_connect();
            }

            $stmt = $conn->prepare($sql);
            $res = $stmt->execute($params);

            if(!$res) {
                return -1;
            }
            $last_id = $conn->lastInsertId();
        } catch (PDOException) {
            return -1;
        }

        return $last_id;
    }

    protected static function db_numRows(string $sql, ?array $params, PDO $conn = null) : int {
        try {
            if (is_null($conn)) {
                $conn = self::db_connect();
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            $res = count($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []);
        } catch (PDOException) {
            return 0;
        }

        return $res;
    }

    protected static function db_contains(string $sql, ?array $params, PDO $conn = null) : bool {
        try {
            if(is_null($conn)) {
                $conn = self::db_connect();
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            $res = ((int) $stmt->fetchColumn()) > 0;
        } catch (PDOException) {
            return false;
        }
        return $res;
    }
}