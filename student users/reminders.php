<?php
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

include "connect.php";

$email = $_SESSION['email'];

// Handle new reminder submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
  $title = mysqli_real_escape_string($connect, $_POST['title']);
  $date = mysqli_real_escape_string($connect, $_POST['date']);
  $time = !empty($_POST['time']) ? mysqli_real_escape_string($connect, $_POST['time']) : null;
  $notes = !empty($_POST['notes']) ? mysqli_real_escape_string($connect, $_POST['notes']) : null;

  $insert = "INSERT INTO reminders (email, title, reminder_date, reminder_time, notes)
             VALUES ('$email', '$title', '$date', '$time', '$notes')";
  mysqli_query($connect, $insert);

  header("Location: reminders.php?added=1");
  exit();
}

// Fetch reminders for logged-in user
$reminders = [];
$query = "SELECT * FROM reminders WHERE email = '$email' ORDER BY reminder_date, reminder_time";
$result = mysqli_query($connect, $query);
if ($result && mysqli_num_rows($result) > 0) {
  $reminders = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WiseWeb - Reminders</title>
  <link rel="stylesheet" href="reminders.css">
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar">
    <div class="nav-container">
      <div class="nav-brand">WiseWeb</div>
      <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="schedule.php">Schedule</a>
        <a href="match.php">Peer Matching</a>
        <a href="messages.php">Messaging</a>
        <a href="reminders.php" class="active">Reminders</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="header">
      <h1>Your Reminders</h1>
      <button class="btn btn-primary" id="addReminderBtn" onclick="document.getElementById('reminderModal').style.display='block'">+ Add Reminder</button>
    </div>

    <!-- Success Message -->
    <?php if (isset($_GET['added'])) { ?>
      <div class="success-message">Reminder added successfully!</div>
    <?php } ?>

    <!-- Reminders List -->
    <div class="reminders-container">
      <?php if (empty($reminders)) { ?>
        <div class="empty-state">
          <div class="empty-icon">📝</div>
          <h3>No reminders yet</h3>
          <p>Add your first reminder to stay organized!</p>
        </div>
      <?php } else { ?>
        <?php foreach ($reminders as $r) { ?>
          <div class="reminder-card">
            <h3><?php echo htmlspecialchars($r['title']); ?></h3>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($r['reminder_date']); ?></p>
            <?php if (!empty($r['reminder_time'])) { ?>
              <p><strong>Time:</strong> <?php echo htmlspecialchars($r['reminder_time']); ?></p>
            <?php } ?>
            <?php if (!empty($r['notes'])) { ?>
              <p><strong>Notes:</strong> <?php echo htmlspecialchars($r['notes']); ?></p>
            <?php } ?>
          </div>
        <?php } ?>
      <?php } ?>
    </div>
  </div>

  <!-- Add Reminder Modal -->
  <div class="modal" id="reminderModal" style="display:none;">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modalTitle">Add New Reminder</h3>
        <button class="close-btn" onclick="document.getElementById('reminderModal').style.display='none'">&times;</button>
      </div>
      <form method="POST" action="reminders.php">
        <div class="form-group">
          <label for="reminderTitle">Title *</label>
          <input type="text" name="title" id="reminderTitle" required>
        </div>
        <div class="form-group">
          <label for="reminderDescription">Notes</label>
          <textarea name="notes" id="reminderDescription" rows="3"></textarea>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="reminderDate">Date *</label>
            <input type="date" name="date" id="reminderDate" required>
          </div>
          <div class="form-group">
            <label for="reminderTime">Time</label>
            <input type="time" name="time" id="reminderTime">
          </div>
        </div>
        <div class="form-actions">
          <button type="button" class="btn btn-secondary" onclick="document.getElementById('reminderModal').style.display='none'">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Reminder</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
