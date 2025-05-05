<?php
session_start();
$conn = new mysqli("localhost", "win", "62TCT7PzRCBybXAp", "win");

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check in database (you can adjust this table/fields later)
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'"; 
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        header('Location: admin.php');
        exit();
    } else {
        $message = 'Invalid Username or Password!';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Kumbati Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f8f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 300px;
            text-align: center;
        }
        input[type="text"], input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #4CAF50;
            border: none;
            color: white;
            border-radius: 5px;
            font-weight: bold;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Login</h2>
    <?php if ($message): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required><br/>
        <input type="password" name="password" placeholder="Password" required><br/>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
