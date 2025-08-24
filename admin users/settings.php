<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "wiseweb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

require_once "functions.php"; // logging

$message = "";

// Handle Add Admin
if (isset($_POST['add_admin'])) {
    $name = $_POST['admin_name'];
    $email = $_POST['email'];
    $password = hash("sha256", $_POST['password']);

    $stmt = $conn->prepare("INSERT INTO settings (admin_name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        $message = "New admin added successfully!";
        logAction($conn, "Added new admin: $name", $_SESSION['admin_name'] ?? "Admin");
    } else {
        $message = "Error: " . $conn->error;
    }
    $stmt->close();
}

// Handle Update Admin
if (isset($_POST['update_admin'])) {
    $id = $_POST['id'];
    $name = $_POST['admin_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed = hash("sha256", $password);
        $stmt = $conn->prepare("UPDATE settings SET admin_name=?, email=?, password=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $hashed, $id);
    } else {
        $stmt = $conn->prepare("UPDATE settings SET admin_name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $id);
    }

    if ($stmt->execute()) {
        $message = "Admin updated successfully!";
        logAction($conn, "Updated admin: $name", $_SESSION['admin_name'] ?? "Admin");
    }
    $stmt->close();
}

// Handle Delete Admin
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM settings WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "Admin deleted!";
        logAction($conn, "Deleted admin ID: $id", $_SESSION['admin_name'] ?? "Admin");
    }
    $stmt->close();
}

// Fetch all admins
$result = $conn->query("SELECT * FROM settings ORDER BY created_at DESC");
$admins = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Settings - WiseWeb</title>
    <link rel="stylesheet" href="admin-dashboard.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-brand">WiseWeb Admin</div>
        <div class="nav-links">
            <a href="admin-dashboard.php">Dashboard</a>
            <a href="manage-students.php">Manage Students</a>
            <a href="view-logs.php">System Logs</a>
            <a href="messages.php">Messages</a>
            <a href="settings.php" class="active">Settings</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="main-container">
    <div class="card">
        <h2 style="text-align:center;">Manage Admin Accounts</h2>

        <?php if (!empty($message)): ?>
            <div class="alert success"><?= $message ?></div>
        <?php endif; ?>

        <!-- Add Admin Form -->
        <form method="POST" class="form">
            <h3>Add New Admin</h3>
            <label>Name</label>
            <input type="text" name="admin_name" required>
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <button type="submit" name="add_admin" class="btn btn-success">Add Admin</button>
        </form>
    </div>

    <div class="card">
        <h3>Existing Admins</h3>
        <table border="1" width="100%" cellpadding="8">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($admins as $admin): ?>
            <tr>
                <td><?= $admin['id'] ?></td>
                <td><?= htmlspecialchars($admin['admin_name']) ?></td>
                <td><?= htmlspecialchars($admin['email']) ?></td>
                <td><?= $admin['created_at'] ?></td>
                <td>
                    <!-- Update Form -->
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                        <input type="text" name="admin_name" value="<?= htmlspecialchars($admin['admin_name']) ?>" required>
                        <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
                        <input type="password" name="password" placeholder="New password (optional)">
                        <button type="submit" name="update_admin" class="btn btn-primary">Update</button>
                    </form>
                    <!-- Delete -->
                    <a href="settings.php?delete=<?= $admin['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this admin?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

</body>
</html>
