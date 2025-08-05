<?php
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

include "connect.php";

$email = $_SESSION['email'];

// Handle Delete
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  mysqli_query($connect, "DELETE FROM schedules WHERE schedule_id = $id AND email = '$email'");
  header("Location: schedule.php?deleted=1");
  exit();
}

// Handle Edit (fetch existing schedule)
$edit_mode = false;
$edit_data = null;

if (isset($_GET['edit'])) {
  $edit_mode = true;
  $edit_id = intval($_GET['edit']);
  $result = mysqli_query($connect, "SELECT * FROM schedules WHERE schedule_id = $edit_id AND email = '$email'");
  if ($result && mysqli_num_rows($result) == 1) {
    $edit_data = mysqli_fetch_assoc($result);
  }
}

// Handle Save/Edit Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $course   = $_POST['course'];
  $date     = $_POST['date'];
  $time     = $_POST['time'];
  $duration = $_POST['duration'];
  $task     = $_POST['task'];
  $notes    = $_POST['notes'];
  $edit_id  = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : null;

  if ($edit_id) {
    $stmt = $connect->prepare("UPDATE schedules SET course=?, schedule_date=?, schedule_time=?, duration=?, task=?, notes=? WHERE schedule_id=? AND email=?");
    $stmt->bind_param("sssissis", $course, $date, $time, $duration, $task, $notes, $edit_id, $email);
  } else {
    $stmt = $connect->prepare("INSERT INTO schedules(email, course, schedule_date, schedule_time, duration, task, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiss", $email, $course, $date, $time, $duration, $task, $notes);
  }

  if (!$stmt->execute()) {
    die("Error saving schedule: " . $stmt->error);
  }
  $stmt->close();

  header("Location: schedule.php?success=1");
  exit();
}

// Fetch user schedules
$schedules = [];
$result = mysqli_query($connect, "SELECT * FROM schedules WHERE email = '$email' ORDER BY schedule_date, schedule_time");
if ($result && mysqli_num_rows($result) > 0) {
  $schedules = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>WiseWeb - Schedule</title>
  <link rel="stylesheet" href="schedule.css">
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar">
    <div class="nav-container">
      <div class="nav-brand">WiseWeb</div>
      <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="schedule.php" class="active">Schedule</a>
        <a href="match.php">Peer Matching</a>
        <a href="messages.php">Messaging</a>
        <a href="reminders.php">Reminders</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container">
    <!-- Page Header -->
    <div class="page-header">
      <h1 class="page-title">Schedule Manager</h1>
      <p class="page-subtitle">Organize your academic life with smart scheduling</p>
    </div>

    <!-- Success Messages -->
    <?php if (isset($_GET['success'])) { ?>
      <div class="success-message">Schedule saved successfully!</div>
    <?php } elseif (isset($_GET['deleted'])) { ?>
      <div class="success-message">Schedule deleted successfully!</div>
    <?php } ?>

    <!-- Add/Edit Schedule Form -->
    <div class="form-section">
      <h2 class="form-title"><?php echo $edit_mode ? "Edit Schedule Item" : "Add New Schedule Item"; ?></h2>
      <form method="POST" action="">
        <div class="form-grid">

          <?php if ($edit_mode): ?>
            <input type="hidden" name="edit_id" value="<?php echo $edit_data['schedule_id']; ?>">
          <?php endif; ?>

          <div class="form-group">
            <label for="course">Course:</label>
            <select name="course" id="course" required>
              <option value="">--Select your course--</option>
              <?php
              $courses = [
                "Computer Science", "Information Technology", "Software Engineering", "Data Science", "Artificial Intelligence",
                "Business Administration", "Accounting", "Finance", "Economics", "Human Resource Management",
                "Marketing", "Law", "Psychology", "Journalism", "Public Relations",
                "Nursing", "Medicine", "Pharmacy", "Education", "Hospitality & Tourism",
                "Architecture", "Engineering", "Agriculture"
              ];
              foreach ($courses as $course) {
                $selected = ($edit_mode && $edit_data['course'] == $course) ? "selected" : "";
                echo "<option value='$course' $selected>$course</option>";
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" name="date" id="date" required value="<?php echo $edit_mode ? $edit_data['schedule_date'] : ''; ?>">
          </div>

          <div class="form-group">
            <label for="time">Time:</label>
            <input type="time" name="time" id="time" required value="<?php echo $edit_mode ? $edit_data['schedule_time'] : ''; ?>">
          </div>

          <div class="form-group">
            <label for="duration">Duration (minutes):</label>
            <input type="number" name="duration" id="duration" min="15" max="480" value="<?php echo $edit_mode ? $edit_data['duration'] : 60; ?>">
          </div>

          <div class="form-group full-width">
            <label for="task">Task/Activity:</label>
            <input type="text" name="task" id="task" required placeholder="e.g., Study, Assignment, Lecture"
              value="<?php echo $edit_mode ? htmlspecialchars($edit_data['task']) : ''; ?>">
          </div>

          <div class="form-group full-width">
            <label for="notes">Additional Notes (optional):</label>
            <textarea name="notes" id="notes"><?php echo $edit_mode ? htmlspecialchars($edit_data['notes']) : ''; ?></textarea>
          </div>
        </div>

        <div style="text-align: center;">
          <button type="submit" class="btn"><?php echo $edit_mode ? "Update Schedule" : "Save Schedule"; ?></button>
        </div>
      </form>
    </div>

    <!-- Schedule List -->
    <div class="schedule-section">
      <h2 class="schedule-title">Your Schedule</h2>
      <div id="scheduleList">
        <?php if (empty($schedules)) { ?>
          <div class="empty-state">No schedule items yet. Add your first item above! 📅</div>
        <?php } else { ?>
          <ul>
            <?php foreach ($schedules as $s) { ?>
              <li>
                <strong><?php echo htmlspecialchars($s['course']); ?></strong> - 
                <?php echo htmlspecialchars($s['task']); ?><br>
                <?php echo htmlspecialchars($s['schedule_date']); ?> at 
                <?php echo htmlspecialchars($s['schedule_time']); ?>
                (<?php echo htmlspecialchars($s['duration']); ?> mins)<br>
                <em><?php echo htmlspecialchars($s['notes']); ?></em><br>
                <a href="schedule.php?edit=<?php echo $s['schedule_id']; ?>" class="btn-small">Edit</a>
                <a href="schedule.php?delete=<?php echo $s['schedule_id']; ?>" class="btn-small" onclick="return confirm('Are you sure you want to delete this schedule?')">Delete</a>
              </li>
            <?php } ?>
          </ul>
        <?php } ?>
      </div>
    </div>
  </div>

</body>
</html>
