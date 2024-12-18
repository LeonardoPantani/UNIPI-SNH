<?php
    $db_host = $_ENV["DB_HOST"];
    $db_user = $_ENV["DB_USER"];
    $db_psw = $_ENV["DB_PASSWORD"];
    $db_name = $_ENV["DB_NAME"];

    $conn = new mysqli($db_host, $db_user, $db_psw, $db_name);
    if($conn->connect_errno) {
        die("Connection failed: " . $conn->connect_error);
    }