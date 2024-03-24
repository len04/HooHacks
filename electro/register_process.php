<?php
// Include database connection
include_once 'db_connect.php';

// Get form data from register.php
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user data into the database
$sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
if (mysqli_query($conn, $sql)) {
    // Registration successful
    header("Location: login.php?message=registered");
    exit();
} else {
    // Registration failed
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

// Close database connection
mysqli_close($conn);
