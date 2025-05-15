<?php
session_start();
include 'db_connection.php'; // Ensure you have a database connection file

// Assuming user_id is stored in the session after login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT full_name, age, gender, blood_type, contact_number FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>