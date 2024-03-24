<?php
// Start the session
session_start();

// Include database connection
include_once 'db_connect.php';

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Retrieve energy usage data for the current user
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM energy_usage WHERE user_id = $user_id ORDER BY year DESC, month DESC LIMIT 12";
    $result = mysqli_query($conn, $query);

    if ($result) {
        if (mysqli_num_rows($result) >= 1) {
            echo "<h3>Energy Usage Data</h3>";
            echo "<table>";
            echo "<tr><th>Month</th><th>Year</th><th>Energy Consumption</th></tr>";
            $rows = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
                echo "<tr>";
                echo "<td>".$row['month']."</td>";
                echo "<td>".$row['year']."</td>";
                echo "<td>".$row['energy_consumption']."</td>";
                echo "</tr>";
            }
            echo "</table>";
            if (mysqli_num_rows($result) >= 2) {

                // Calculate percentage reduction
                $percentage_reduction = (-$rows[0]['energy_consumption'] + $rows[1]['energy_consumption']) / $rows[1]['energy_consumption'] * 100;
                echo "<p>Percentage reduction from previous month: ".number_format($percentage_reduction, 2)." %</p>";

            }

            // Query to retrieve energy usage data ordered by month and year
            $query = "SELECT * FROM energy_usage WHERE user_id = $user_id ORDER BY year DESC, month DESC";
            $result = mysqli_query($conn, $query);

            if ($result) {
                // Array to store the first two entries for the same month
                $entries = array();

                // Flag to indicate if we found the first entry for the same month
                $found_first_entry = false;

                // Iterate through the rows
                while ($row = mysqli_fetch_assoc($result)) {
                    // Check if we found the first entry for the same month
                    if (!$found_first_entry) {
                        $entries[] = $row; // Add the first entry
                        $found_first_entry = true;
                    } else {
                        // Check if the current month is the same as the first entry's month
                        if ($row['month'] == $entries[0]['month']) {
                            $entries[] = $row; // Add the second entry
                            break; // Stop iterating after finding the second entry
                        }
                    }
                }

                // Check if we found at least two entries for the same month
                if (count($entries) >= 2) {
                    // Calculate the percentage reduction
                    $percentage_reduction = (-$entries[0]['energy_consumption'] + $entries[1]['energy_consumption']) / $entries[1]['energy_consumption'] * 100;
                    echo "<p>Percentage reduction based on the first two entries for the same month: ".number_format($percentage_reduction, 2)." %</p>";
                } else {
                    echo "Insufficient energy usage data for the same month. Please enter more data.";
                }
            }

            // Add button to join a community
            echo '<button onclick="window.location.href=\'community.php\'">Want to join a community?</button>';

            // Query to retrieve user's communities
            $user_communities_query = "SELECT c.* FROM communities c INNER JOIN community_members cm ON c.id = cm.community_id WHERE cm.user_id = $user_id";
            $user_communities_result = mysqli_query($conn, $user_communities_query);

            if ($user_communities_result && mysqli_num_rows($user_communities_result) > 0) {
                echo "<h3>My Communities</h3>";
                echo "<ul>";
                while ($community_row = mysqli_fetch_assoc($user_communities_result)) {
                    echo "<li>";
                    echo htmlspecialchars($community_row['community_name']);
                    echo "<button onclick=\"window.location.href='community_details.php?community_id=" . $community_row['id'] . "'\">More</button>";
                    echo "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>You are not a member of any community.</p>";
            }

        } else {
            echo "Insufficient energy usage data. Please enter more data.";
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // Display form to input new energy data
    ?>
    <h3>Input New Energy Data</h3>
    <form action="dashboard_process.php" method="post">
        <label for="month">Month:</label>
        <input type="text" name="month" required><br><br>
        <label for="year">Year:</label>
        <input type="text" name="year" required><br><br>
        <label for="energy_consumption">Energy Consumption:</label>
        <input type="text" name="energy_consumption" required><br><br>
        <button type="submit">Submit</button>
    </form>
    <?php
} else {
    echo "User ID not found in session. Please log in.";
}

// Close database connection
mysqli_close($conn);
?>
</body>
</html>
