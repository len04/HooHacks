

<?php
// Include database connection
include_once 'db_connect.php';

// Get form data from login.php
$email = $_POST['email'];
$password = $_POST['password'];

// Retrieve user data from the database
$sql = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // User found, verify password
    $row = mysqli_fetch_assoc($result);
    if (password_verify($password, $row['password'])) {
        // Password is correct, login successful
        session_start();
        $_SESSION['username'] = $row['username'];
        $_SESSION['user_id'] = $row['id']; // Assuming 'id' is the column name for user ID
        header("Location: dashboard.php");
        exit();
    } else {
        // Password is incorrect
        header("Location: login.php?message=incorrect_password");
        exit();
    }
} else {
    // User not found
    header("Location: login.php?message=user_not_found");
    exit();
}

// Close database connection

?>
