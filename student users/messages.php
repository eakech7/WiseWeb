<?php
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

include "connect.php";
$email = $_SESSION['email'];

// Handle message send in the same file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_email'], $_POST['message'])) {
  $receiver = mysqli_real_escape_string($connect, $_POST['receiver_email']);
  $msg = mysqli_real_escape_string($connect, $_POST['message']);
  $timestamp = date("Y-m-d H:i:s");

  $insert = "INSERT INTO messages (sender_email, receiver_email, message, timestamp, seen)
             VALUES ('$email', '$receiver', '$msg', '$timestamp', 0)";
  mysqli_query($connect, $insert);
  // Redirect back to same chat after sending
  header("Location: messages.php?chat=" . urlencode($receiver));
  exit();
}

// Fetch contacts
$contacts = [];
$contactQuery = "SELECT u.fullname, u.email 
                 FROM matches m 
                 JOIN users u ON (u.email = m.user1_email OR u.email = m.user2_email)
                 WHERE '$email' IN (m.user1_email, m.user2_email) 
                   AND u.email != '$email'";
$result = mysqli_query($connect, $contactQuery);
if ($result && mysqli_num_rows($result) > 0) {
  $contacts = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Determine active chat
$activeChat = isset($_GET['chat']) ? $_GET['chat'] : (count($contacts) > 0 ? $contacts[0]['email'] : null);

// Fetch messages
$messages = [];
if ($activeChat) {
  $msgQuery = "SELECT sender_email, receiver_email, message, timestamp 
               FROM messages 
               WHERE (sender_email = '$email' AND receiver_email = '$activeChat') 
                  OR (sender_email = '$activeChat' AND receiver_email = '$email') 
               ORDER BY timestamp ASC";
  $msgResult = mysqli_query($connect, $msgQuery);
  if ($msgResult && mysqli_num_rows($msgResult) > 0) {
    $messages = mysqli_fetch_all($msgResult, MYSQLI_ASSOC);
  }
}

// Get name from email
function getNameByEmail($email, $connect) {
  $query = "SELECT fullname FROM users WHERE email = '$email'";
  $res = mysqli_query($connect, $query);
  if ($res && mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    return $row['fullname'];
  }
  return $email;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>WiseWeb - Messages</title>
  <link rel="stylesheet" href="messages.css" />
</head>
<body>
  <!-- Navigation Bar -->
  <nav class ="navbar">
    <div class="nav-container">
      <div class="nav-brand">WiseWeb</div>
      <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="schedule.php">Schedule</a>
        <a href="match.php">Peer Matching</a>
        <a href="messages.php" class="active">Messaging</a>
        <a href="reminders.php">Reminders</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="page-header">
      <h1 class="page-title">Messages</h1>
      <p class="page-subtitle">Connect and collaborate with your study partners</p>
    </div>

    <div class="chat-container">
      <!-- Contacts Sidebar -->
      <div class="contacts-sidebar">
        <div class="contacts-header">Conversations</div>
        <div class="search-box">
          <input type="text" id="contactSearch" placeholder="Search contacts..." />
        </div>
        <div class="contacts-list" id="contactsList">
          <?php if (empty($contacts)) { ?>
            <div class="empty-chat">No contacts yet. Match with peers first!</div>
          <?php } else { ?>
            <?php foreach ($contacts as $c) { ?>
              <a href="messages.php?chat=<?php echo urlencode($c['email']); ?>" 
                 class="contact-item <?php echo ($activeChat == $c['email']) ? 'active' : ''; ?>">
                <div class="contact-avatar">
                  <?php echo strtoupper(substr($c['fullname'], 0, 1)); ?>
                </div>
                <div class="contact-info">
                  <div class="contact-name"><?php echo htmlspecialchars($c['fullname']); ?></div>
                  <div class="contact-status"><?php echo htmlspecialchars($c['email']); ?></div>
                </div>
              </a>
            <?php } ?>
          <?php } ?>
        </div>
      </div>

      <!-- Chat Area -->
      <div class="chat-area">
        <?php if (!$activeChat) { ?>
          <div class="empty-chat">
            <div class="empty-chat-icon">💬</div>
            <h3>No messages yet</h3>
            <p>Match with peers to start chatting.</p>
          </div>
        <?php } else { ?>
          <div class="chat-header">
            <div class="chat-user-info">
              <div class="chat-user-avatar">
                <?php echo strtoupper(substr(getNameByEmail($activeChat, $connect), 0, 1)); ?>
              </div>
              <div class="chat-user-details">
                <h3><?php echo htmlspecialchars(getNameByEmail($activeChat, $connect)); ?></h3>
              </div>
            </div>
          </div>

          <div class="messages-container" id="messagesContainer">
            <?php if (empty($messages)) { ?>
              <div class="empty-chat">No messages yet. Say hello!</div>
            <?php } else { ?>
              <?php foreach ($messages as $m) { ?>
                <div class="message <?php echo ($m['sender_email'] == $email) ? 'sent' : 'received'; ?>">
                  <div class="message-bubble">
                    <?php echo htmlspecialchars($m['message']); ?>
                    <span class="message-time"><?php echo date("H:i", strtotime($m['timestamp'])); ?></span>
                  </div>
                </div>
              <?php } ?>
            <?php } ?>
          </div>

          <div class="message-input-container">
            <form class="message-input-form" method="POST">
              <input type="hidden" name="receiver_email" value="<?php echo htmlspecialchars($activeChat); ?>">
              <input type="text" class="message-input" name="message" placeholder="Type a message..." required />
              <button type="submit" class="send-btn">Send</button>
            </form>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</body>
</html>
