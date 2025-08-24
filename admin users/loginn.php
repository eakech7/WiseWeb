<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "wiseweb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

require_once "settings.php";
require_once "functions.php"; // for logging


$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $hashed = hash("sha256", $password);

    $stmt = $conn->prepare("SELECT * FROM settings WHERE email=? AND password=? LIMIT 1");
    $stmt->bind_param("ss", $email, $hashed);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin) {
        // Successful login
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['admin_name'];
        logAction($conn, "Admin logged in", $admin['admin_name']);

        $_SESSION['admin_email'] = $admin['email'];

        logAction($conn, "Admin logged in", $admin['admin_name']);

        header("Location: admin-dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - WiseWeb</title>
    <link rel="stylesheet" href="admin-dashboard.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f4f6f9;
            font-family: Arial, sans-serif;
        }
        .login-box {
            width: 400px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-box label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        .login-box input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .login-box button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #007BFF;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .login-box button:hover {
            background: #0056b3;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>WiseWeb Admin Login</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
