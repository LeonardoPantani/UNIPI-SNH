<?php
function db_connect($host = null, $user = null, $psw = null, $name = null)
{
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
