<?php
namespace App\Models;

require_once __DIR__ . '/../libs/utils/db/db_connect.php';

class User
{
    private $conn;

    public function __construct()
    {
        global $conn; 
        $this->conn = $conn;
    }

    public function getAllUsers()
    {
        $sql = "SELECT * FROM user";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            return $users;
        }
        return [];
    }
}
?>