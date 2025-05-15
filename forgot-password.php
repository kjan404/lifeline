<?php
require 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(50)); // Generate secure token

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Save token in database
        $stmt = $conn->prepare("UPDATE users SET reset_token=? WHERE email=?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        $message = "Password reset link (simulate): <a href='reset_password.php?token=$token'>Reset Password</a>";
    } else {
        $message = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f4f4;
        }

        .form-container {
            background: #fff;
            padding: 30px 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 10px;
            width: 350px;
            text-align: center;
        }

        .form-container h2 {
            margin-bottom: 20px;
        }

        .form-container input[type="email"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .form-container button {
            width: 100%;
            padding: 12px;
            background-color: #2e86de;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #1e5fa3;
        }

        .message {
            margin-top: 20px;
            color: #333;
            word-wrap: break-word;
        }

        .message a {
            color: #2e86de;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="form-container">
    <form method="POST">
        <h2>Forgot Password</h2>
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Send Reset Link</button>
    </form>

    <?php if (!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
</div>

</body>
</html>
