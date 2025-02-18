<?php

namespace App\Utils;

require_once __DIR__ . '/../log/logger.php';

use PDO;
use PDOException;
use Exception;

abstract class DBConnection {
    private static function db_connect($host = null, $user = null, $psw = null, $name = null) : PDO {
        $host = $host ?? getenv("DB_HOST");
        $user = $user ?? getenv("DB_USER");
        $psw  = $psw  ?? getenv("DB_PASSWORD");
        $name = $name ?? getenv("DB_NAME");

        // NOTE: PHP uses 'latin1' charset, whereas MySQL uses 'utf8' or 'utf8mb4' charset.
        $dsn = "mysql:host=$host;dbname=$name;charset=latin1";
        $conn = new PDO($dsn, $user, $psw, array(PDO::ATTR_TIMEOUT => 1, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

        return $conn;
    }

    public static function newDBInstance() : ?PDO {
        $logger = getLogger('db');

        try {
            return self::db_connect();
        } catch (PDOException $e) {
            $logger->info('newDBInstance: ' . $e->getMessage());
            return null;
        }
    }

    public static function db_transaction(PDO $conn) : bool {
        $logger = getLogger('db');

        try {
            return $conn->beginTransaction();
        } catch (PDOException $e) {
            $logger->info('db_transaction: ' . $e->getMessage());
            return false;
        }
    }

    public static function db_isTransactionActive(PDO $conn) : bool {
        return $conn->inTransaction();
    }

    public static function db_commit(PDO $conn) : bool {
        $logger = getLogger('db');

        try {
            return $conn->commit();
        } catch(PDOException $e) {
            $logger->info('db_commit: ' . $e->getMessage());
            return false;
        }
    }

    public static function db_rollback(PDO $conn) : bool {
        $logger = getLogger('db');

        try {
            return $conn->rollBack();
        } catch(PDOException $e) {
            $logger->info('db_rollback: ' . $e->getMessage());
            return false;
        }
    }

    protected static function db_fetchOne(string $sql, ?array $params, PDO $conn = null) : array {
        $logger = getLogger('db');

        try {
            if(is_null($conn)) {
                $conn = self::db_connect();
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException $e) {
            $logger->info('db_fetchOne: ' . $e->getMessage());
            return [];
        }

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    protected static function db_fetchAll(string $sql, ?array $params, PDO $conn = null) : array {
        $logger = getLogger('db');

        try {
            if(is_null($conn)) {
                $conn = self::db_connect();
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException $e) {
            $logger->info('db_fetchAll: ' . $e->getMessage());
            return [];
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    protected static function db_getOutcome(string $sql, ?array $params, PDO $conn = null) : bool {
        $logger = getLogger('db');

        try {
            if (is_null($conn)) {
                $conn = self::db_connect();
            }

            $stmt = $conn->prepare($sql);
            $res = $stmt->execute($params);
        } catch (PDOException $e) {
            $logger->info('db_getOutcome: ' . $e->getMessage());
            return false;
        }

        return $res;
    }

    protected static function db_getLastInsertId(string $sql, ?array $params, PDO $conn = null) : int {
        $logger = getLogger('db');

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
        } catch (PDOException $e) {
            $logger->info('db_getLastInsertId: ' . $e->getMessage());
            return -1;
        }

        return $last_id;
    }

    protected static function db_numRows(string $sql, ?array $params, PDO $conn = null) : int {
        $logger = getLogger('db');

        try {
            if (is_null($conn)) {
                $conn = self::db_connect();
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            $res = count($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []);
        } catch (PDOException $e) {
            $logger->info('db_numRows: ' . $e->getMessage());
            return 0;
        }

        return $res;
    }

    protected static function db_contains(string $sql, ?array $params, PDO $conn = null) : bool {
        $logger = getLogger('db');

        try {
            if(is_null($conn)) {
                $conn = self::db_connect();
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            $res = ((int) $stmt->fetchColumn()) > 0;
        } catch (PDOException $e) {
            $logger->info('db_contains: ' . $e->getMessage());
            return false;
        }

        return $res;
    }
}