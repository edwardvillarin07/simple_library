<?php
$conn = new mysqli("localhost", "win", "62TCT7PzRCBybXAp", "win");

$message = ""; // Store message here

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $student_id = $_POST['student_id'];
    $date = $_POST['date'];
    $time_in = ($action === 'in') ? $_POST['time_in'] : null;
    $time_out = ($action === 'out') ? $_POST['time_out'] : null;

    // Optional fields
    $name = isset($_POST['name']) ? $_POST['name'] : null;
    $purpose = isset($_POST['purpose']) ? $_POST['purpose'] : null;

    // Check if the record already exists for this student_id and date
    $check_query = "SELECT * FROM kumbati_attendance WHERE student_id = ? AND date = ?";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bind_param("ss", $student_id, $date);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // If the record exists, update the time_out if action is "out"
        if ($action === 'out') {
            // Step 1: Insert the record into the logs table
            $log_query = "INSERT INTO kumbati_attendance_logs (student_id, name, purpose, date, time_in, time_out) 
                          SELECT student_id, name, purpose, date, time_in, ? FROM kumbati_attendance WHERE student_id = ? AND date = ?";
            $stmt_log = $conn->prepare($log_query);
            $stmt_log->bind_param("sss", $time_out, $student_id, $date);
            
            if ($stmt_log->execute()) {
                // Step 2: Delete the record from kumbati_attendance table
                $delete_query = "DELETE FROM kumbati_attendance WHERE student_id = ? AND date = ?";
                $stmt_delete = $conn->prepare($delete_query);
                $stmt_delete->bind_param("ss", $student_id, $date);

                if ($stmt_delete->execute()) {
                    $message = "<p class='success'>✅ Time Out recorded and attendance record deleted!</p>";
                } else {
                    $message = "<p class='error'>❌ Error deleting record: " . $conn->error . "</p>";
                }
            } else {
                $message = "<p class='error'>❌ Error logging the record: " . $conn->error . "</p>";
            }
        } else {
            $message = "<p class='error'>❌ Record already exists for Time In. Please check.</p>";
        }
    } else {
        // If no record exists, insert a new row with both time_in and time_out if available
        $stmt_insert = $conn->prepare("INSERT INTO kumbati_attendance (name, student_id, purpose, date, time_in, time_out) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("ssssss", $name, $student_id, $purpose, $date, $time_in, $time_out);

        if ($stmt_insert->execute()) {
            $message = "<p class='success'>✅ Attendance recorded successfully!</p>";
        } else {
            $message = "<p class='error'>❌ Error: " . $conn->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Access Record</title>
    <style>
        /* --- (your CSS styles remain unchanged, keeping it clean) --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f2f2f2; display: flex; align-items: center; justify-content: center; height: 100vh; padding: 10px; }
        .form-container { background: white; padding: 30px 40px; border-radius: 12px; box-shadow: 0 0 20px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        h2 { text-align: center; margin-bottom: 20px; }
        label { font-weight: bold; margin-bottom: 5px; display: block; }
        input[type="text"], input[type="date"], input[type="time"], textarea { width: 100%; padding: 12px; margin-top: 5px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 6px; }
        textarea { resize: none; }
        .radio-group { margin-bottom: 15px; display: flex; justify-content: space-between; }
        .radio-group label { display: inline-block; margin: 0 10px; }
        button { width: 100%; padding: 12px; background-color: #009578; color: white; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; }
        button:hover { background-color: #007e63; }
        .success { background-color: #d4edda; color: #155724; padding: 10px; text-align: center; border-radius: 5px; margin-bottom: 15px; }
        .error { background-color: #f8d7da; color: #721c24; padding: 10px; text-align: center; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="form-container">
        <?php if (!empty($message)) echo $message; ?>
        
        <h2>Library Access Record</h2>
        <form method="POST" action="">
            <div class="radio-group">
                <label><input type="radio" name="action" value="in" checked onchange="toggleFields()"> Time In</label>
                <label><input type="radio" name="action" value="out" onchange="toggleFields()"> Time Out</label>
            </div>

            <div id="nameGroup">
                <label>Name:</label>
                <input type="text" name="name" id="name" required>
            </div>

            <label>Student ID:</label>
            <input type="text" name="student_id" required>

            <div id="purposeGroup">
                <label>Purpose:</label>
                <textarea name="purpose" id="purpose" rows="3" required></textarea>
            </div>

            <label>Date:</label>
            <input type="date" name="date" id="date" required>

            <div id="timeInGroup">
                <label>Time In:</label>
                <input type="time" name="time_in" id="time_in">
            </div>

            <div id="timeOutGroup" style="display:none;">
                <label>Time Out:</label>
                <input type="time" name="time_out" id="time_out">
            </div>

            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
        function toggleFields() {
            const action = document.querySelector('input[name="action"]:checked').value;
            const nameGroup = document.getElementById('nameGroup');
            const purposeGroup = document.getElementById('purposeGroup');
            const timeInGroup = document.getElementById('timeInGroup');
            const timeOutGroup = document.getElementById('timeOutGroup');

            if (action === "in") {
                nameGroup.style.display = "block";
                purposeGroup.style.display = "block";
                timeInGroup.style.display = "block";
                timeOutGroup.style.display = "none";
                document.getElementById('name').required = true;
                document.getElementById('purpose').required = true;
                document.getElementById('time_in').required = true;
                document.getElementById('time_out').required = false;
            } else {
                nameGroup.style.display = "none";
                purposeGroup.style.display = "none";
                timeInGroup.style.display = "none";
                timeOutGroup.style.display = "block";
                document.getElementById('name').required = false;
                document.getElementById('purpose').required = false;
                document.getElementById('time_in').required = false;
                document.getElementById('time_out').required = true;
            }
        }

        window.onload = function() {
            toggleFields();

            // Set default date and time
            const now = new Date();
            document.getElementById('date').value = now.toISOString().split('T')[0];

            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const currentTime = `${hours}:${minutes}`;
            document.getElementById('time_in').value = currentTime;
            document.getElementById('time_out').value = currentTime;
        };
    </script>

</body>
</html>
