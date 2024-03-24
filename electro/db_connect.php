<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_accounts";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
