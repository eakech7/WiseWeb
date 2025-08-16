<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: manage-users.php");
    exit;
}

// Fetch user details
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
$user = mysqli_fetch_assoc($query);

// Update if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);

    mysqli_query($conn, "UPDATE users SET name='$name', email='$email', course='$course' WHERE id=$id");
    header("Location: manage-users.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User - WiseWeb</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <nav class="navbar">
    <div class="nav-container">
      <div class="nav-brand">WiseWeb Admin</div>
      <div class="nav-links">
        <a href="admin-dashboard.php">Dashboard</a>
        <a href="manage-users.php" class="active">Manage Users</a>
        <a href="view-schedules.php">View Schedules</a>
        <a href="../logout.php">Logout</a>
      </div>
    </div>
  </nav>

  <div class="main-container">
    <div class="content-card">
      <div class="card-header">
        <h2 class="card-title">Edit User</h2>
      </div>
      <form method="post">
        <p><label>Name: <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required></label></p>
        <p><label>Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></label></p>
        <p><label>Course: <input type="text" name="course" value="<?= htmlspecialchars($user['course']) ?>" required></label></p>
        <button type="submit" class="btn btn-success">Save Changes</button>
      </form>
    </div>
  </div>
</body>
</html>
