<?php

// error_reporting(0);
// ini_set('display_errors', 0);

//FOR LATER
$username = "alex";
$password = "900235181";
$servername = "cs-db.fandm.edu";

$dbname = "alex_rafael";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>