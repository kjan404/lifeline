<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['admin_email']);
    $password = trim($_POST['admin_password']);

    // Static credentials for admin
    $valid_username = 'admin';
    $valid_password = 'admin123';

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['admin'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        // Use session to show message on form
        $_SESSION['error'] = "Invalid username or password.";
        header("Location: admin_login.html");
        exit();
    }
}
?>
