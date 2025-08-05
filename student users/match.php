<?php
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

include "connect.php";

$email = $_SESSION['email'];

// Get the logged-in user's course
$userQuery = "SELECT course FROM users WHERE email = '$email'";
$userResult = mysqli_query($connect, $userQuery);
$user = mysqli_fetch_assoc($userResult);

if (!$user) {
  die("User not found.");
}

$course = $user['course'];

// Handle match deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_email'])) {
  $deleteEmail = $_POST['delete_email'];
  $deleteQuery = "DELETE FROM matches 
                  WHERE (user1_email = '$email' AND user2_email = '$deleteEmail') 
                     OR (user1_email = '$deleteEmail' AND user2_email = '$email')";
  mysqli_query($connect, $deleteQuery);
  header("Location: match.php?deleted=1");
  exit();
}

// Handle match creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['peer_email'])) {
  $peerEmail = $_POST['peer_email'];

  // Avoid duplicate matches
  $checkQuery = "SELECT * FROM matches 
                 WHERE (user1_email = '$email' AND user2_email = '$peerEmail') 
                    OR (user1_email = '$peerEmail' AND user2_email = '$email')";
  $checkResult = mysqli_query($connect, $checkQuery);

  if ($checkResult && mysqli_num_rows($checkResult) == 0) {
    $insertQuery = "INSERT INTO matches(user1_email, user2_email) VALUES('$email', '$peerEmail')";
    mysqli_query($connect, $insertQuery);
  }

  // Refresh page
  header("Location: match.php?matched=1");
  exit();
}

// Find potential peers in the same course with overlapping schedules (excluding current user & already matched ones)
$peers = [];
$peersQuery = "
  SELECT DISTINCT u.fullname, u.email, u.course
  FROM users u
  JOIN schedules s1 ON s1.email = '$email'
  JOIN schedules s2 ON s2.email = u.email
  WHERE u.course = '$course'
    AND u.email != '$email'
    AND s1.schedule_date = s2.schedule_date
    AND s1.schedule_time < ADDTIME(s2.schedule_time, SEC_TO_TIME(s2.duration*60))
    AND ADDTIME(s1.schedule_time, SEC_TO_TIME(s1.duration*60)) > s2.schedule_time
    AND u.email NOT IN (
      SELECT CASE 
               WHEN user1_email = '$email' THEN user2_email 
               ELSE user1_email 
             END 
      FROM matches 
      WHERE '$email' IN (user1_email, user2_email)
    )";
$result = mysqli_query($connect, $peersQuery);
if ($result && mysqli_num_rows($result) > 0) {
  $peers = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Fetch active matches
$matches = [];
$matchesQuery = "SELECT u.fullname, u.email 
                 FROM matches m 
                 JOIN users u ON (u.email = m.user1_email OR u.email = m.user2_email)
                 WHERE ('$email' IN (m.user1_email, m.user2_email)) 
                   AND u.email != '$email'";
$result = mysqli_query($connect, $matchesQuery);
if ($result && mysqli_num_rows($result) > 0) {
  $matches = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>WiseWeb - Peer Matching</title>
  <link rel="stylesheet" href="match.css">
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar">
    <div class="nav-container">
      <div class="nav-brand">WiseWeb</div>
      <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="schedule.php">Schedule</a>
        <a href="match.php" class="active">Peer Matching</a>
        <a href="messages.php">Messaging</a>
        <a href="reminders.php">Reminders</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container">
    <!-- Page Header -->
    <div class="header">
      <h1>Peer Matching</h1>
      <p class="page-subtitle">Connect with peers in your course who share available time for study sessions</p>
    </div>

    <!-- Success Message -->
    <?php if (isset($_GET['matched'])) { ?>
      <div class="success-message">Match created successfully!</div>
    <?php } elseif (isset($_GET['deleted'])) { ?>
      <div class="success-message">Match deleted successfully.</div>
    <?php } ?>

    <!-- Potential Peers -->
    <div class="form-section">
      <h2 class="form-title" style="color:white;">Available Peers in <?php echo htmlspecialchars($course); ?></h2>
      <?php if (empty($peers)) { ?>
        <div class="empty-state">No peers available right now.</div>
      <?php } else { ?>
        <div class="matches-grid">
          <?php foreach ($peers as $p) { ?>
            <div class="match-card">
              <div class="match-header">
                <div class="match-avatar"><?php echo strtoupper(substr($p['fullname'], 0, 1)); ?></div>
                <div class="match-info">
                  <h3><?php echo htmlspecialchars($p['fullname']); ?></h3>
                  <p style="color: #666;"><?php echo htmlspecialchars($p['email']); ?></p>
                </div>
              </div>
              <form method="POST" action="">
                <input type="hidden" name="peer_email" value="<?php echo htmlspecialchars($p['email']); ?>">
                <button type="submit" class="btn btn-primary">Match</button>
              </form>
            </div>
          <?php } ?>
        </div>
      <?php } ?>
    </div>

    <!-- Active Matches -->
    <div class="schedule-section">
      <h2 class="schedule-title" style="color: white;">Your Active Matches</h2>
      <?php if (empty($matches)) { ?>
        <div class="empty-state">No active matches yet. Start matching above!</div>
      <?php } else { ?>
        <div class="matches-grid">
          <?php foreach ($matches as $m) { ?>
            <div class="match-card">
              <div class="match-header">
                <div class="match-avatar"><?php echo strtoupper(substr($m['fullname'], 0, 1)); ?></div>
                <div class="match-info">
                  <h3><?php echo htmlspecialchars($m['fullname']); ?></h3>
                  <p style="color: #666;"><?php echo htmlspecialchars($m['email']); ?></p>
                </div>
              </div>
              <form method="POST" action="">
                <input type="hidden" name="delete_email" value="<?php echo htmlspecialchars($m['email']); ?>">
                <button type="submit" class="btn btn-danger">Delete</button>
              </form>
            </div>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  </div>

</body>
</html>
