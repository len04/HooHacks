<?php
// Start session
session_start();

// Include database connection
include_once 'db_connect.php';

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $community_name = $_POST['community_name'];
        $user_id = $_SESSION['user_id'];

        // Check if the community name is not empty
        if (!empty($community_name)) {
            // Check if the community name already exists
            $check_query = "SELECT * FROM communities WHERE community_name = '$community_name'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) == 0) {
                // Insert new community into the database
                $insert_query = "INSERT INTO communities (community_name) VALUES ('$community_name')";
                if (mysqli_query($conn, $insert_query)) {
                    // Get the ID of the newly created community
                    $community_id = mysqli_insert_id($conn);
                    
                    // Add the current user as a member of the new community
                    $join_query = "INSERT INTO community_members (user_id, community_id) VALUES ($user_id, $community_id)";
                    if (mysqli_query($conn, $join_query)) {
                        echo "Community created successfully!";
                    } else {
                        echo "Error joining the community: " . mysqli_error($conn);
                    }
                } else {
                    echo "Error creating the community: " . mysqli_error($conn);
                }
            } else {
                echo "Community name already exists. Please choose a different name.";
            }
        } else {
            echo "Community name cannot be empty.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Community</title>
</head>
<body>
    <h2>Create a New Community</h2>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validateForm()">
        <label for="community_name">Community Name:</label><br>
        <input type="text" name="community_name" id="community_name" required><br><br>
        <button type="submit">Create Community</button>
    </form>

    <script>
        function validateForm() {
            var communityName = document.getElementById("community_name").value;
            if (communityName.trim() == "") {
                alert("Community name cannot be empty.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
