<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "wiseweb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Example reports: total students, role breakdown, course breakdown
$total_students = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'];

$roles = $conn->query("SELECT role, COUNT(*) AS count FROM students GROUP BY role");
$courses = $conn->query("SELECT course, COUNT(*) AS count FROM students GROUP BY course");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports</title>
  <link rel="stylesheet" href="admin-dashboard.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
  <div class="nav-container">
    <div class="nav-brand">WiseWeb Admin</div>
    <div class="nav-links">
      <a href="admin-dashboard.html">Dashboard</a>
      <a href="manage-students.php">Manage Students</a>
      <a href="reports.php" class="active">Reports</a>
      <a href="logout.php" class="logout">Logout</a>
    </div>
  </div>
</nav>

<div class="main-container">
  <div class="dashboard-header">
    <h1>Reports</h1>
    <p>Overview of student data</p>
  </div>

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-number"><?= $total_students ?></div>
      <div class="stat-label">Total Students</div>
    </div>
  </div>

  <!-- Reports Content -->
  <div class="content-grid">

    <!-- Role Breakdown -->
    <div class="content-card">
      <div class="card-header">
        <h3 class="card-title">Students by Role</h3>
      </div>
      <table class="styled-table">
        <thead>
          <tr>
            <th>Role</th>
            <th>Count</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $roles->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['role']) ?></td>
              <td><?= $row['count'] ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Course Breakdown -->
    <div class="content-card">
      <div class="card-header">
        <h3 class="card-title">Students by Course</h3>
      </div>
      <table class="styled-table">
        <thead>
          <tr>
            <th>Course</th>
            <th>Count</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $courses->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['course']) ?></td>
              <td><?= $row['count'] ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

</body>
</html>
