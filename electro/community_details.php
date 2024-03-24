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

// Check if community ID is provided in the URL
if (!isset($_GET['community_id'])) {
    echo "Community ID not provided.";
    exit();
}

// Retrieve community ID from the URL
$community_id = $_GET['community_id'];

// Retrieve community details from the database
$query_community = "SELECT * FROM communities WHERE id = $community_id";
$result_community = mysqli_query($conn, $query_community);

if ($result_community && mysqli_num_rows($result_community) > 0) {
    $community = mysqli_fetch_assoc($result_community);
} else {
    echo "Community not found.";
    exit();
}

// Retrieve the number of users in the community
$query_users_count = "SELECT COUNT(*) as total_users FROM community_members WHERE community_id = $community_id";
$result_users_count = mysqli_query($conn, $query_users_count);
if ($result_users_count && mysqli_num_rows($result_users_count) > 0) {
    $users_count_data = mysqli_fetch_assoc($result_users_count);
    $users_count = $users_count_data['total_users'];
} else {
    $users_count = 0;
}

// Retrieve the latest energy usage data for each user in the community
$query_energy_data = "SELECT energy_consumption FROM energy_usage WHERE user_id IN (SELECT user_id FROM community_members WHERE community_id = $community_id) ORDER BY year DESC, month DESC LIMIT $users_count";
$result_energy_data = mysqli_query($conn, $query_energy_data);

if ($result_energy_data && mysqli_num_rows($result_energy_data) > 0) {
    $total_energy_consumption = 0;
    while ($row = mysqli_fetch_assoc($result_energy_data)) {
        $total_energy_consumption += $row['energy_consumption'];
    }

    // Calculate the average energy consumption per user
    $average_energy_consumption_per_user = $total_energy_consumption / $users_count;

} else {
    $average_energy_consumption_per_user = 0;
}

// Retrieve the average energy consumption per month for the community, grouped by year
$query_avg_energy_per_month = "SELECT AVG(energy_consumption) as avg_energy, month, year FROM energy_usage WHERE user_id IN (SELECT user_id FROM community_members WHERE community_id = $community_id) GROUP BY year, month ORDER BY year, month";
$result_avg_energy_per_month = mysqli_query($conn, $query_avg_energy_per_month);

// Initialize arrays to store month and average energy consumption data
$years_data = []; // Array to hold data for each year
$current_year = null;

if ($result_avg_energy_per_month && mysqli_num_rows($result_avg_energy_per_month) > 0) {
    while ($row = mysqli_fetch_assoc($result_avg_energy_per_month)) {
        $year = $row['year'];
        $month = $row['month'];
        $avg_energy = $row['avg_energy'];

        // Check if the current year has changed
        if ($current_year !== $year) {
            // Start a new array for the current year
            $current_year = $year;
            $years_data[$current_year] = [];
        }

        // Store the month and its corresponding average energy consumption in the current year's array
        $years_data[$current_year][$month] = $avg_energy;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Details</title>
    <!-- Include Chart.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
</head>
<body>
    <h2>Community Details</h2>

    <h3>Community Name: <?php echo $community['community_name']; ?></h3>
    <p>Number of Users: <?php echo $users_count; ?></p>
    <p>Average Energy Consumption per User (Latest Month): <?php echo $average_energy_consumption_per_user; ?></p>

    <!-- Bar chart for average energy consumption per month -->
    <canvas id="avgEnergyChart" width="400" height="200"></canvas>

    <script>
        // Get the canvas element
        var ctx = document.getElementById('avgEnergyChart').getContext('2d');

        // Create the bar chart
        var avgEnergyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(range(1, 12)); ?>, // Months
                datasets: [
                    <?php foreach ($years_data as $year => $months) : ?>
                        {
                            label: '<?php echo $year; ?>',
                            data: <?php echo json_encode(array_values($months)); ?>,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                    <?php endforeach; ?>
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>

<?php
// Close database connection
mysqli_close($conn);
?>
