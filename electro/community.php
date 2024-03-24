<?php
// Start session
session_start();

// Include database connection
include_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if form is submitted to create a new community
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // If "Create New Community" is selected
    if ($_POST['selected_community'] === "create_new_community") {
        // Redirect user to create community page
        header("Location: create_community.php");
        exit();
    } else {
        // Get selected community ID from the form
        $community_id = $_POST['selected_community'];
        
        // Get user ID from session
        $user_id = $_SESSION['user_id'];

        // Add user to the selected community
        $join_query = "INSERT INTO community_members (user_id, community_id) VALUES ($user_id, $community_id)";
        if (mysqli_query($conn, $join_query)) {
            $success_message = "You have successfully joined the community!";
        } else {
            $error_message = "Error joining the community: " . mysqli_error($conn);
        }
    }
}

// Retrieve list of existing communities
$query = "SELECT * FROM communities";
$result = mysqli_query($conn, $query);

// Array to store existing communities
$communities = array();

// Fetch communities and store in array
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $communities[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Page</title>
</head>
<body>
    <h2>Join or Create a Community</h2>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="selected_community">Select or search for your community:</label><br>
        <select name="selected_community" id="selected_community">
            <option value="" disabled selected hidden>Select Community</option>
            <?php
            // Display existing communities in dropdown menu
            foreach ($communities as $community) {
                echo "<option value='" . $community['id'] . "'>" . htmlspecialchars($community['community_name']) . "</option>";
            }
            ?>
            <option value="create_new_community">Create New Community</option>
        </select><br><br>
        <button type="submit">Join/Create Community</button>
    </form>

    <!-- Display success or error message -->
    <?php if (isset($success_message)) : ?>
        <p><?php echo $success_message; ?></p>
    <?php elseif (isset($error_message)) : ?>
        <p><?php echo $error_message; ?></p>
    <?php endif; ?>

</body>
</html>
