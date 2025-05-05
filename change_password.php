<?php
session_start();
$conn = new mysqli("localhost", "win", "62TCT7PzRCBybXAp", "win");

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $sql = "SELECT password FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if ($current_password === $user['password']) { // no password_verify
            if ($new_password === $confirm_password) {
                
                $update = "UPDATE users SET password = '$new_password' WHERE username = '$username'";
                if ($conn->query($update)) {
                    $message = "✅ Password changed successfully!";
                } else {
                    $message = "❌ Error updating password.";
                }
            } else {
                $message = "⚠️ New passwords do not match.";
            }
        } else {
            $message = "⚠️ Current password is incorrect.";
        }
    } else {
        $message = "❌ User not found.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #74ebd5 0%, #acb6e5 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .card h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 14px;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }
        .btn {
            background: #4CAF50;
            color: #fff;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #45a049;
        }
        .message {
            margin-top: 15px;
            font-size: 14px;
            color: #d32f2f;
            font-weight: bold;
        }
        .success {
            color: #2e7d32;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>🔑 Change Password</h2>

    <?php if ($message != ''): ?>
        <div class="message <?= (strpos($message, '✅') !== false) ? 'success' : '' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>Current Password</label>
            <input type="password" name="current_password" required>
        </div>

        <div class="input-group">
            <label>New Password</label>
            <input type="password" name="new_password" required>
        </div>

        <div class="input-group">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required>
        </div>

        <button type="submit" class="btn">Update Password</button>
    </form>
</div>

</body>
</html>

<?php
$conn->close();
?>
