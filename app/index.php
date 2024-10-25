<?php
	echo "<p>server works!</p>";
	
	$db_host = $_ENV["DB_HOST"];
	$db_user = $_ENV["DB_USER"];
	$db_psw = $_ENV["DB_PASSWORD"];
	$db_name = $_ENV["DB_NAME"];

	$conn = new mysqli($db_host, $db_user, $db_psw, $db_name);
	if($conn->connect_errno) {
		die("Connection failed: " . $conn->connect_error);
	}

	echo "<p>db connection works!</p>";
	
	$query = "CREATE TABLE IF NOT EXISTS user( id int primary key auto_increment, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL )";
	$res = $conn->query($query);
	
	if($conn->errno) {
		die("Invalid query: " . $conn->error);
	}
	
	echo "<p>create table works!</p>";

	$query = "INSERT INTO user (username, password) VALUES ('test', 'test')";
	$res = $conn->query($query);
	
	if($conn->errno) {
		die("Invalid query: " . $conn->error);
	}

	echo "<p>insert query works!</p>";
	
	$query = "SELECT * FROM user";
	$res = mysqli_query($conn, $query);
	
	if($conn->errno) {
		die("Invalid query: " . $conn->error);
	}

	echo "<p>Users:</p>";
	echo "<lu>";

	while($row = $res->fetch_assoc()) {
		echo "<li>" . $row["id"] . ", ". $row["username"] . "</li>";
	}

	echo "</lu>";

	echo "<p>insert query works!</p>";

	$conn->close();
