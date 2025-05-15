<?php
$host = "localhost"; // Change if needed
$user = "root"; // Default username in XAMPP
$pass = ""; // Default password is empty
$dbname = "users_db";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>