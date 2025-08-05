<?php
session_start(); 
include "connect.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $query = "SELECT * FROM users WHERE email = '$email'";
  $run_query = mysqli_query($connect, $query);

  if ($run_query) {
    if (mysqli_num_rows($run_query) > 0) {
      $record = mysqli_fetch_assoc($run_query);
      $password_hash = $record['password'];

      if (password_verify($password, $password_hash)) {
        $_SESSION['email'] = $email;
        header("Location: dashboard.php");
        exit();
      } else {
        $error = "Wrong credentials";
      }
    } else {
      $error = "User does not exist";
    }
  } else {
    die(mysqli_error($connect));
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>WiseWeb - Login</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      height: 100vh;  
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-container {
      background: white;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 400px;
    }

    .login-container h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #667eea;
    }

    label {
      margin-top: 15px;
      display: block;
      color: #333;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    input[type="submit"] {
      width: 100%;
      padding: 10px;
      margin-top: 20px;
      background-color: #667eea;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }

    input[type="submit"]:hover {
      background-color: #4e0fa7;
    }

    .formtext {
      text-align: center;
      margin-top: 10px;
    }

    .formtext a {
      color: #667eea;
      text-decoration: none;
    }

    .error {
      background-color: #ffdddd;
      color: #a10000;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
      text-align: center;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h2>Login to WiseWeb</h2>

    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="post">
      <label>Email</label>
      <input type="email" name="email" placeholder="Enter email" required>

      <label>Password</label>
      <input type="password" name="password" placeholder="Enter password" required>

      <input type="submit" name="login" value="Login">

      <div class="formtext">Don't have an account? <a href="register.php">Signup</a></div>
      <div class="formtext">Forgot your password? <a href="recovery.html">Reset Password</a></div>
    </form>
  </div>

</body>
</html>
