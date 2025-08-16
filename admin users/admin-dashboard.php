<?php
session_start();
require 'db.php';

// Protect page: only logged-in admins
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch stats
$userCountQuery = mysqli_query($conn, "SELECT COUNT(*) AS total_users FROM users");
$userCount = mysqli_fetch_assoc($userCountQuery)['total_users'];

$scheduleCountQuery = mysqli_query($conn, "SELECT COUNT(*) AS total_schedules FROM schedules");
$scheduleCount = mysqli_fetch_assoc($scheduleCountQuery)['total_schedules'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>WiseWeb Admin Dashboard</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="nav-container">
      <div class="nav-brand">WiseWeb Admin</div>
      <div class="nav-links">
        <a href="admin-dashboard.php" class="active">Dashboard</a>
        <a href="manage-users.php">Manage Users</a>
        <a href="view-schedules.php">View Schedules</a>
        <a href="../logout.php">Logout</a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="main-container">
    <div class="dashboard-header">
      <h1>Welcome, Admin!</h1>
      <p>Use this panel to manage WiseWeb users and view schedules.</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-number"><?= $userCount ?></div>
        <div class="stat-label">Registered Users</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?= $scheduleCount ?></div>
        <div class="stat-label">Schedules Created</div>
      </div>
    </div>
  </div>
</body>
</html>
