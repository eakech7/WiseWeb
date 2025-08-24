<?php
// manage-admins.php
$host = "localhost";
$user = "root";   // default for XAMPP
$pass = "";       // leave blank unless you set a MySQL password
$db   = "wiseweb";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add admin
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_admin'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO admins (name, email, password) VALUES ('$name', '$email', '$password')";
    if ($conn->query($sql) === TRUE) {
        $message = " New admin added successfully!";
    } else {
        $message = " Error: " . $conn->error;
    }
}

// Delete admin
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM admins WHERE id=$id");
    header("Location: manage-admins.php");
    exit;
}

// Fetch admins
$result = $conn->query("SELECT id, name, email, created_at FROM admins ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Admins - WiseWeb</title>
  <link rel="stylesheet" href="../css/admin-dashboard.css"> <!-- Keep using your theme -->
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
    <div class="nav-container">
      <div class="nav-brand">WiseWeb Admin</div>
      <div class="nav-links">
        <a href="../admin-dashboard.html">Dashboard</a>
        <a href="manage-students.php">Manage Students</a>
        <a href="manage-admins.php" class="active">Manage Admins</a>
        <a href="../view-logs.php">System Logs</a>
        <a href="../messages.php">Messages</a>
        <a href="../settings.php">Settings</a>
        <a href="../logout.php">Logout</a>
      </div>
    </div>
  </nav>

  <main class="main-content">
    <div class="container">
      <h1>Manage Admins</h1>

      <?php if (!empty($message)) : ?>
        <p class="status-message"><?= $message ?></p>
      <?php endif; ?>

      <!-- Add Admin Form -->
      <section class="card">
        <h2>Add New Admin</h2>
        <form method="POST" action="manage-admins.php" class="form">
          <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
          </div>
          <button type="submit" name="add_admin" class="btn">Add Admin</button>
        </form>
      </section>

      <!-- Admins Table -->
      <section class="card">
        <h2>Admin List</h2>
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Created At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= $row['created_at'] ?></td>
              <td><a href="?delete=<?= $row['id'] ?>" class="btn-danger" onclick="return confirm('Are you sure?')">Delete</a></td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </section>
    </div>
  </main>

</body>
</html>
