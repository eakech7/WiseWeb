<?php
session_start();
require 'db.php';

// Protect page: only logged-in admins
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch users
$sql = "SELECT * FROM users ORDER BY name";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users - WiseWeb</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <!-- Navbar -->
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

  <!-- Main Content -->
  <div class="main-container">
    <div class="dashboard-header">
      <h1>Registered Users</h1>
      <p>Manage user details and access permissions</p>
    </div>

    <div class="content-card">
      <div class="card-header">
        <h2 class="card-title">User List</h2>
      </div>
      <table border="1" cellpadding="8" cellspacing="0" style="width:100%; background:white; border-radius:8px;">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Course</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['course']) ?></td>
                <td>
                  <a href="edit-user.php?id=<?= $row['id'] ?>" class="btn btn-success">Edit</a>
                  <a href="delete-user.php?id=<?= $row['id'] ?>" class="btn btn-secondary" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" style="text-align:center;">No users found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
