<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        die("Passwords do not match.");
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Update password
    $stmt = $conn->prepare("UPDATE users SET password=?, reset_token=NULL WHERE reset_token=?");
    $stmt->bind_param("ss", $hashed, $token);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Password successfully updated. You can now <a href='login.html'>login</a>.";
    } else {
        echo "Failed to update password. Token may have expired.";
    }
}
?>
