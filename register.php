<?php
include 'db_connect.php'; // Ensure this sets $conn properly

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname  = $_POST['lastname'];
    $email     = $_POST['email'];
    $password  = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $token     = bin2hex(random_bytes(50));
    $status    = "pending";

    $sql = "INSERT INTO users (firstname, lastname, email, password, token, status) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    if (!$stmt->bind_param("ssssss", $firstname, $lastname, $email, $password, $token, $status)) {
        die("Bind param failed: " . $stmt->error);
    }

    if ($stmt->execute()) {
        $to      = $email;
        $subject = "Email Verification - Life Line System";
        $link    = "http://yourdomain.com/verify.php?email=$email&token=$token";
        $message = "Hi $firstname,\n\nClick the link below to verify your email:\n$link";
        $headers = "From: no-reply@yourdomain.com";

        if (mail($to, $subject, $message, $headers)) {
            echo "Registration successful! Please check your email to verify.";
        } else {
            echo "Failed to send verification email.";
        }
    } else {
        echo "Execute failed: " . $stmt->error;
    }

    $stmt->close();
}
?>
