<?php
session_start();

require_once "settings.php";
require_once "functions.php";


// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Start a new session just for the message
session_start();
$_SESSION['logout_message'] = "You have successfully logged out.";

logAction($conn, "Admin logged out", $_SESSION['admin_name'] ?? "Unknown");


// Redirect to loginn.php
header("Location: loginn.php"); // you can change this to login.php if you add one later
exit;
?>
