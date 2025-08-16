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
      $row = mysqli_fetch_assoc($run_query);

      //  password_verify to handle hashed passwords
      if (password_verify($password, $row['password'])) {
        $_SESSION['user'] = $row;
        header("Location: profile1.php");
        exit;
      } else {
        $error_message = "Wrong credentials!";
      }
    } else {
      $error_message = "User does not exist!";
    }
  } else {
    $error_message = "Query failed: " . mysqli_error($connect);
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>

    <!--  Single error box for PHP + JS messages -->
    <?php if (!empty($error_message)): ?>
      <div id="error-message" class="error" style="color:red; margin-bottom:10px;">
        <?php echo $error_message; ?>
      </div>
    <?php else: ?>
      <div id="error-message" class="error" style="display:none; color:red; margin-bottom:10px;"></div>
    <?php endif; ?>

    <form method="post" id="loginForm">
      <label>Email</label>
      <input type="email" name="email" id="email" placeholder="Enter email" required>

      <label>Password</label>
      <input type="password" name="password" id="password" placeholder="Enter password" required>

      <!-- Caps Lock warning -->
      <div id="caps-lock-warning" style="display:none; color:red; font-size:12px; margin-top:5px;">
         Caps Lock is ON
      </div>

      <input type="submit" name="login" value="Login">

      <div class="formtext">Don't have an account? <a href="register.php">Signup</a></div>
      <div class="formtext">Forgot your password? <a href="recovery.html">Reset Password</a></div>
    </form>
  </div>

  <!--  Link JS at bottom -->
  <script src="login.js"></script>
</body>
</html>
