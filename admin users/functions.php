<?php
// functions.php

// Function to log an admin/system action into the system_logs table
function logAction($conn, $action, $user = "System") {
    if (!$conn) {
        return false; // Prevent errors if no DB connection
    }

    $stmt = $conn->prepare("INSERT INTO system_logs (action, user) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $action, $user);
        $stmt->execute();
        $stmt->close();
        return true;
    } else {
        return false; // Something went wrong
    }
}
?>
