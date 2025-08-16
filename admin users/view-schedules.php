<?php
session_start();
require 'db.php';

// Protect page: only logged-in admins
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch schedules
$sql = "SELECT users.name AS user_name, schedules.day, schedules.time, schedules.activity 
        FROM schedules 
        JOIN users ON schedules.user_id = users.id 
        ORDER BY schedules.day, schedules.time";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Schedules - WiseWeb</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="nav-container">
      <div class="nav-brand">WiseWeb Admin</div>
      <div class="nav-links">
        <a href="admin-dashboard.php">Dashboard</a>
        <a href="manage-users.php">Manage Users</a>
        <a href="view-schedules.php" class="active">View Schedules</a>
        <a href="../logout.php">Logout</a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="main-container">
    <div class="dashboard-header">
      <h1>All User Schedules</h1>
      <p>View and manage schedules from registered users</p>
    </div>

    <div class="content-card">
      <div class="card-header">
        <h2 class="card-title">Schedules</h2>
      </div>
      <table border="1" cellpadding="8" cellspacing="0" style="width:100%; background:white; border-radius:8px;">
        <thead>
          <tr>
            <th>User</th>
            <th>Day</th>
            <th>Time</th>
            <th>Activity</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= htmlspecialchars($row['user_name']) ?></td>
                <td><?= htmlspecialchars($row['day']) ?></td>
                <td><?= htmlspecialchars($row['time']) ?></td>
                <td><?= htmlspecialchars($row['activity']) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" style="text-align:center;">No schedules found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
