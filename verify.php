<?php
include 'db.php';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    $sql = "SELECT * FROM users WHERE email = ? AND token = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $update = $conn->prepare("UPDATE users SET status = 'verified', token = NULL WHERE email = ?");
        $update->bind_param("s", $email);
        $update->execute();
        echo "Email verified successfully. You can now log in.";
    } else {
        echo "Invalid or expired token.";
    }
}
?>
