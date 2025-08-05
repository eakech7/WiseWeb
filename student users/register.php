<?php
include "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $fullname = $_POST['fullname'];
  $course = $_POST['course'];
  $dob = $_POST['dob'];
  $gender = $_POST['gender'];
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $check_query = "SELECT * FROM users WHERE email = '$email'";
  $check_result = mysqli_query($connect, $check_query);

  if (mysqli_num_rows($check_result) > 0) {
    $error = "User already exists!";
  } else {
    $query = "INSERT INTO users (fullname, course, dob, gender, username, email, password) 
              VALUES ('$fullname', '$course', '$dob', '$gender', '$username', '$email', '$password')";

    if (mysqli_query($connect, $query)) {
      header("Location: login.php");
      exit();
    } else {
      $error = "Registration failed: " . mysqli_error($connect);
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>WiseWeb - Register</title>
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

    .register-container {
      background: white;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 500px;
    }

    .register-container h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #667eea;
    }

    label {
      margin-top: 10px;
      display: block;
      color: #333;
    }

    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    input[type="submit"] {
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

  <div class="register-container">
    <h2>Create a WiseWeb Account</h2>

    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="post">
      <label>Full Name</label>
      <input type="text" name="fullname" placeholder="Enter full name" required>

      <label>Course</label>
      <input type="text" name="course" placeholder="e.g. DBIT" required>

      <label>Date of Birth</label>
      <input type="date" name="dob" required>

      <label>Gender</label>
      <select name="gender" required>
        <option value="">Select gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
      </select>

      <label>Username</label>
      <input type="text" name="username" placeholder="Choose a username" required>

      <label>Email</label>
      <input type="email" name="email" placeholder="Enter email" required>

      <label>Password</label>
      <input type="password" name="password" placeholder="Create password" required>

      <input type="submit" value="Register">

      <div class="formtext">Already have an account? <a href="login.php">Login</a></div>
    </form>
  </div>

</body>
</html>
