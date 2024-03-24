<?php
// Start the session
session_start();

// Include database connection
include_once 'db_connect.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $month = $_POST['month'];
    $year = $_POST['year'];
    $energy_consumption = $_POST['energy_consumption'];
    
    // Retrieve user ID from session
    session_start();
    $user_id = $_SESSION['user_id'];

    // Insert new energy data into the database
    $query = "INSERT INTO energy_usage (user_id, month, year, energy_consumption) VALUES ('$user_id', '$month', '$year', '$energy_consumption')";
    if (mysqli_query($conn, $query)) {
        // Redirect to dashboard with success message
        header("Location: dashboard.php?message=success");
        exit();
    } else {
        // Redirect to dashboard with error message
        header("Location: dashboard.php?message=error");
        exit();
    }
}

// Close database connection
mysqli_close($conn);
