<?php
include 'db.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    if (empty($email)) {
        $error_message = "Email is required.";
    } else {
        // For demonstration purposes, we'll just show a success message.
        // In a real application, you would generate a reset token, save it to the database,
        // and send an email to the user with a reset link.
        $success_message = "If an account with that email exists, a password reset link has been sent.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Tech NIG</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-wrapper">
        <div class="form-container">
            <h2>Forgot Password</h2>
            <p>Enter your email address and we will send you a link to reset your password.</p>
            <?php if (!empty($error_message)): ?>
                <p class="error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <p class="success"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <form method="POST" action="forgot_password.php">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit">Send Reset Link</button>
            </form>
            <div class="links">
                <a href="login.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
