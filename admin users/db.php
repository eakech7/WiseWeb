<?php
// db.php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "wiseweb"; 

$conn = mysqli_connect($host, $user, $pass, $wiseweb);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
