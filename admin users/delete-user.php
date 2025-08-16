<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if ($id) {
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
}

header("Location: manage-users.php");
exit;
