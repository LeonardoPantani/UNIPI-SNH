<?php

namespace App\Models;
use PDO;

require_once __DIR__ . '/../libs/utils/db/db_connect.php';

class User
{
    private $conn;

    public function __construct()
    {
        $this->conn = db_connect();
    }

    public function getAllUsers()
    {
        $sql = "SELECT * FROM users";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $users ?: [];
    }
}