<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "win", "62TCT7PzRCBybXAp", "win");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle timeout and log the action
if (isset($_POST['student_id']) && isset($_POST['time_out'])) {
    $student_id = $_POST['student_id'];
    $time_out = $_POST['time_out'];

    // Check if time_out is null and set a default value if needed
    if (empty($time_out)) {
        $time_out = '00:00:00'; // Default time_out if not provided
    }

    // Log the action in the kumbati_attendance_logs table
    $log_action = "Student ID: $student_id timed out";
    $log_query = "INSERT INTO kumbati_attendance_logs (student_id, name, purpose, date, time_in, time_out, deleted_at) 
                  VALUES (?, ?, ?, ?, ?, ?, current_timestamp())";
    $stmt_log = $conn->prepare($log_query);

    // Define the values for each field
    $log_name = "Attendance Log"; // Static name for the log
    $log_purpose = "Student timed out"; // Purpose of the log
    $log_date = date("Y-m-d"); // Current date in 'YYYY-MM-DD' format
    $log_time_in = "00:00:00"; // Default time_in, can be adjusted if available

    // Bind parameters and execute the query
    $stmt_log->bind_param("ssssss", $student_id, $log_name, $log_purpose, $log_date, $log_time_in, $time_out);
    $stmt_log->execute();
    $stmt_log->close();

    // Redirect to a confirmation page or show a success message
    header("Location: attendance_confirmation.php?message=" . urlencode("Log entry created successfully."));
    exit();
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']); // Always use intval to prevent SQL injection
    $deleteSql = "DELETE FROM kumbati_attendance WHERE id = $deleteId";
    if ($conn->query($deleteSql) === TRUE) {
        $message = "Record deleted successfully!";
    } else {
        $message = "Error deleting record: " . $conn->error;
    }
    header("Location: admin.php?message=" . urlencode($message));
    exit();
}

// Initialize empty arrays for chart data
$data = [];
$labels = [];

// Get search term (if any)
$searchStudentId = isset($_GET['student_id']) ? $_GET['student_id'] : '';

// SQL query for chart data
$sqlChart = "
    SELECT 
        date,
        COUNT(time_in) AS time_in_count,
        COUNT(time_out) AS time_out_count
    FROM kumbati_attendance
    GROUP BY date
    ORDER BY date ASC
";

$resultChart = $conn->query($sqlChart);

while ($row = $resultChart->fetch_assoc()) {
    $labels[] = $row['date'];
    $data['time_in'][] = $row['time_in_count'];
    $data['time_out'][] = $row['time_out_count'];
}

// SQL query for table data
$sqlTable = "
    SELECT id, name, student_id, purpose, date, time_in, time_out
    FROM kumbati_attendance
    WHERE student_id = '$searchStudentId' OR '$searchStudentId' = ''
    ORDER BY date ASC, time_in ASC, time_out ASC
";

$resultTable = $conn->query($sqlTable);

// SQL query for log data
$sqlLog = "
    SELECT id, student_id, name, purpose, date, time_in, time_out
    FROM kumbati_attendance_logs
    ORDER BY date DESC, time_in DESC, time_out DESC
";

$resultLog = $conn->query($sqlLog);

//for detele 
// Handle multiple delete for logs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_ids'])) {
    $deleteIds = $_POST['delete_ids']; // This is an array of IDs
    if (!empty($deleteIds)) {
        $ids = implode(",", array_map('intval', $deleteIds)); // Sanitize IDs
        $deleteSql = "DELETE FROM kumbati_attendance_logs WHERE id IN ($ids)";
        if ($conn->query($deleteSql) === TRUE) {
            $message = "Selected log records deleted successfully!";
        } else {
            $message = "Error deleting records: " . $conn->error;
        }
    } else {
        $message = "No records selected for deletion.";
    }
    header("Location: admin.php?message=" . urlencode($message));
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>KUMBATI Attendance Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f8f8f8;
            text-align: center;
        }
        .card {
            width: 100%;
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .card h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        input[type="text"] {
            padding: 8px;
            border-radius: 5px;
            margin: 20px 0;
            width: 100%;
            max-width: 300px;
        }
        button {
            padding: 8px 16px;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .table-container {
            max-width: 100%;
            overflow-x: auto;
            margin-top: 20px;
        }
        @media screen and (max-width: 768px) {
            table, th, td {
                font-size: 14px;
            }
            .card {
                padding: 15px;
            }
            .card h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

<!-- Success or error message -->
<?php if (isset($_GET['message'])): ?>
    <div class="message"><?= htmlspecialchars($_GET['message'] ?? '') ?></div>
<?php endif; ?>

<!-- Chart Card -->
<div class="card">
    <h2>📊 Library Access Record</h2>
    
    <!-- Chart -->
    <canvas id="attendanceChart"></canvas>
</div>

<!-- Attendance Records Card -->
<div class="card">
    <h2>📋 Attendance Records</h2>
    
    <!-- Search Form -->
    <form method="get">
        <input type="text" name="student_id" placeholder="Search by Student ID" value="<?= htmlspecialchars($searchStudentId) ?>">
        <button type="submit">Search</button>
    </form>

    <!-- Show message if no records found -->
    <?php if ($resultTable->num_rows == 0 && $searchStudentId != ''): ?>
        <p>No records found for Student ID: <?= htmlspecialchars($searchStudentId) ?></p>
    <?php endif; ?>

    <!-- Table Container -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Student ID</th>
                    <th>Purpose</th>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    <?php while ($row = $resultTable->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['student_id']) ?></td>
            <td><?= htmlspecialchars($row['purpose']) ?></td>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><?= htmlspecialchars($row['time_in']) ?></td>
            <td><?= ($row['time_out'] === NULL) ? '' : htmlspecialchars($row['time_out']) ?></td> <!-- Check for NULL and display blank -->
            <td>
                <form method="post" onsubmit="return confirm('Are you sure you want to delete this record?');">
                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                    <button type="submit" class="delete-button">Delete</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>

        </table>
    </div>
</div>

<!-- Log Records Card -->
<!-- Log Records Card -->
<div class="card">
    <h2>📝 Log Records</h2>

    <!-- Form for multiple delete -->
    <form method="post" onsubmit="return confirm('Are you sure you want to delete selected records?');">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th> <!-- Select All Checkbox -->
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Purpose</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultLog->fetch_assoc()): ?>
                        <tr>
                            <td><input type="checkbox" name="delete_ids[]" value="<?= htmlspecialchars($row['id']) ?>"></td>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['student_id']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['purpose']) ?></td>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td><?= htmlspecialchars($row['time_in']) ?></td>
                            <td><?= htmlspecialchars($row['time_out']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <br>
        <button type="submit">Delete Selected</button>
    </form>
</div>

<script>
document.getElementById('select-all').addEventListener('click', function(event) {
    const checkboxes = document.querySelectorAll('input[name="delete_ids[]"]');
    for (const checkbox of checkboxes) {
        checkbox.checked = event.target.checked;
    }
});
</script>

<!-- Chart.js Script -->
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [
                {
                    label: 'Time In',
                    backgroundColor: '#4CAF50',
                    data: <?= json_encode($data['time_in']) ?>
                },
                {
                    label: 'Time Out',
                    backgroundColor: '#FF5722',
                    data: <?= json_encode($data['time_out']) ?>
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: { title: { display: true, text: 'Date' } },
                y: { beginAtZero: true, title: { display: true, text: 'Number of Students' } }
            }
        }
    });
</script>

<!-- Links -->
<a href="logout.php">
    <button style="margin-top: 10px;">Logout</button>
</a>
<a href="change_password.php">
    <button style="margin-top: 10px;">Change Password</button>
</a>

</body>
</html>

<?php
// Close the connection
$conn->close();
?>
