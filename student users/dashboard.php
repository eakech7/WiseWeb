<?php
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

include "connect.php";

$email = $_SESSION['email'];

// Get user's full name
$userQuery = "SELECT fullname FROM users WHERE email = '$email'";
$userResult = mysqli_query($connect, $userQuery);
$user = mysqli_fetch_assoc($userResult);

// Count: Upcoming sessions
$sessionsQuery = "SELECT COUNT(*) as total FROM schedules WHERE email = '$email'";
$sessionsResult = mysqli_query($connect, $sessionsQuery);
$upcomingSessions = $sessionsResult ? mysqli_fetch_assoc($sessionsResult)['total'] : 0;

// Count: Reminders
$remindersQuery = "SELECT COUNT(*) as total FROM reminders WHERE email = '$email'";
$remindersResult = mysqli_query($connect, $remindersQuery);
$totalReminders = $remindersResult ? mysqli_fetch_assoc($remindersResult)['total'] : 0;

// Count: New messages
$messagesQuery = "SELECT COUNT(*) as total FROM messages WHERE receiver_email = '$email' AND seen = 0";
$messagesResult = mysqli_query($connect, $messagesQuery);
$newMessages = $messagesResult ? mysqli_fetch_assoc($messagesResult)['total'] : 0;

// Count: Active matches
$connectionsQuery = "SELECT COUNT(*) as total FROM matches WHERE user1_email = '$email' OR user2_email = '$email'";
$connectionsResult = mysqli_query($connect, $connectionsQuery);
$activeConnections = $connectionsResult ? mysqli_fetch_assoc($connectionsResult)['total'] : 0;

// Get recent activity (last 10 from matches, messages, reminders)
$activity = [];

$activityQuery = "
  (SELECT 'Connected with ' AS type, u.fullname AS detail, m.created_at AS time
   FROM matches m
   JOIN users u ON (CASE 
                     WHEN m.user1_email = '$email' THEN m.user2_email 
                     ELSE m.user1_email 
                   END) = u.email
   WHERE m.user1_email = '$email' OR m.user2_email = '$email')
  UNION
  (SELECT 'New message from ' AS type, u.fullname AS detail, msg.timestamp AS time
   FROM messages msg
   JOIN users u ON msg.sender_email = u.email
   WHERE msg.receiver_email = '$email')
  UNION
  (SELECT 'Reminder: ' AS type, r.title AS detail, r.reminder_date AS time
   FROM reminders r
   WHERE r.email = '$email')
  ORDER BY time DESC
  LIMIT 10
";

$activityResult = mysqli_query($connect, $activityQuery);
if ($activityResult) {
  while ($row = mysqli_fetch_assoc($activityResult)) {
    $activity[] = $row;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>WiseWeb Dashboard</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar">
  <div class="nav-container">
    <div class="nav-brand">WiseWeb</div>
    <div class="nav-links">
      <a href="dashboard.php" class="active">Dashboard</a>
      <a href="schedule.php">Schedule</a>
      <a href="match.php">Peer Matching</a>
      <a href="messages.php">Messaging</a>
      <a href="reminders.php">Reminders</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="main-container">
  <section id="dashboard" class="page-section active">
    <div class="dashboard-header">
      <h1>Welcome, <?php echo htmlspecialchars($user['fullname']); ?> </h1>
      <p>Your personalized learning and connection platform</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-number"><?php echo $activeConnections; ?></div>
        <div class="stat-label">Active Connections</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo $upcomingSessions; ?></div>
        <div class="stat-label">Upcoming Sessions</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo $totalReminders; ?></div>
        <div class="stat-label">Reminders</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo $newMessages; ?></div>
        <div class="stat-label">New Messages</div>
      </div>
    </div>

    <div class="content-grid">
      <div class="content-card">
        <div class="card-header">
          <h3 class="card-title">Recent Activity</h3>
        </div>
        <ul class="item-list">
          <?php if (!empty($activity)): ?>
            <?php foreach ($activity as $item): ?>
              <li>
                <span><?php echo htmlspecialchars($item['type'] . $item['detail']); ?></span>
                <small><?php echo date("M d, Y H:i", strtotime($item['time'])); ?></small>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li><span>No recent activity</span></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </section>
</div>

<script src="dashboard.js"></script>
</body>
</html>
