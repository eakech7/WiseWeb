<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "wiseweb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Message placeholder
$message = "";

// Handle Add Student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $course = $_POST['course'];
    $year_of_study = $_POST['year_of_study'];
    $role = $_POST['role'];

    // Check duplicate email
    $check = $conn->prepare("SELECT id FROM students WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "Email already exists! Please use a different one.";
    } else {
        $stmt = $conn->prepare("INSERT INTO students (name, email, phone, course, year_of_study, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssis", $name, $email, $phone, $course, $year_of_study, $role);
        $stmt->execute();
        $stmt->close();
        $message = "Student added successfully!";
    }
    $check->close();
}

// Handle Delete Student
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $message = "Student deleted successfully!";
}

// Fetch all students
$result = $conn->query("SELECT * FROM students");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
    <link rel="stylesheet" href="admin-dashboard.css">
    <style>
        /* Table */
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 0.95rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            border-radius: 12px;
            overflow: hidden;
        }
        .styled-table thead {
            background: linear-gradient(135deg, #003366, #00509E);
            color: #fff;
        }
        .styled-table th, .styled-table td {
            padding: 12px 15px;
            text-align: left;
        }
        .styled-table tbody tr {
            border-bottom: 1px solid #ddd;
        }
        .styled-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .styled-table tbody tr:hover {
            background-color: #eef5ff;
        }

        /* Card Style */
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 20px;
            margin-bottom: 25px;
        }

        /* Form inside card */
        .form label {
            display: block;
            margin-top: 12px;
            font-weight: 600;
            color: #333;
        }
        .form input, .form select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 0.95rem;
        }
        .form input:focus, .form select:focus {
            border-color: #00509E;
            outline: none;
            box-shadow: 0 0 4px rgba(0,80,158,0.3);
        }
        .form button {
            margin-top: 15px;
            padding: 12px;
            background: #00509E;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
        }
        .form button:hover {
            background: #003366;
        }

        /* Messages */
        .message {
            padding: 10px 15px;
            margin: 15px 0;
            border-radius: 8px;
            font-weight: 500;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        /* Center the Add Student form card */
  .form-card {
    max-width: 500px;  /* smaller width */
    margin: 0 auto 30px auto; /* center horizontally + space below */
    padding: 25px 30px;
}

    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-brand">WiseWeb Admin</div>
        <div class="nav-links">
            <a href="admin-dashboard.html">Dashboard</a>
            <a href="manage-students.php" class="active">Manage Students</a>
            <a href="reports.php">Reports</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <h1 style="text-align: center;">Manage Students</h1>

    <?php if (!empty($message)): ?>
        <div class="message <?= strpos($message, 'exists') !== false ? 'error' : 'success' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- Add Student Form -->
    <div class="card form-card">
        <h2 style="text-align:center;">Add Student</h2>
        <form method="POST" class="form">
            <label>Name</label>
            <input type="text" name="name" required>

            <label>Email</label> 
            <input type="email" name="email" required>

            <label>Phone</label>
            <input type="text" name="phone">

            <label>Course</label>
            <input type="text" name="course">

            <label>Year of Study</label>
            <input type="number" name="year_of_study" min="1" max="6">

            <label>Role</label>
            <select name="role">
                <option value="student">Student</option>
                <option value="prefect">Prefect</option>
                <option value="Class Rep">Class Rep</option>
                <option value="Club Member">Club Member</option>
            </select>

            <button type="submit" name="add_student">Add Student</button>
        </form>
    </div>

    <!-- Student List -->
    <div class="card">
        <h2>Student List</h2>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['course']) ?></td>
                        <td><?= $row['year_of_study'] ?></td>
                        <td><?= $row['role'] ?></td>
                        <td>
                            <a href="edit-student.php?id=<?= $row['id'] ?>" class="btn small">Edit</a>
                            <a href="manage-students.php?delete=<?= $row['id'] ?>" class="btn small danger" onclick="return confirm('Delete this student?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
s