<?php
// Start session
session_start();

// Include database connection
include_once 'db_connect.php';

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Check if the form is submitted and selected_community is set
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_community'])) {
        // Get selected community ID from the form
        $community_id = $_POST['selected_community'];
        
        // Get user ID from session
        $user_id = $_SESSION['user_id'];

        // Add user to the selected community
        $insert_query = "INSERT INTO community_members (user_id, community_id) VALUES ($user_id, $community_id)";
        if (mysqli_query($conn, $insert_query)) {
            echo "You have successfully joined the community!";
        } else {
            echo "Error joining the community: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid request.";
    }
} else {
    echo "User not logged in.";
}

// Close database connection
mysqli_close($conn);
?>
