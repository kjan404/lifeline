<?php
$host = "localhost"; // Change if needed
$user = "root"; // Default username in XAMPP
$pass = ""; // Default password is empty
$dbname = "blood_donations_and_requests_info";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>