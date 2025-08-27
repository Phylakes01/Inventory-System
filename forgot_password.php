<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Tech NIG</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Forgot Password</h2>
        <p style="margin-bottom: 1.5rem; color: #555;">Enter your email and we'll send a link to reset your password.</p>
        <?php if (!empty($message)): ?>
            <p class="<?php echo $message_type; ?>"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form method="POST" action="forgot_password.php">
            <div class="form-group"><label for="email">Email:</label><input type="email" id="email" name="email" required></div>
            <button type="submit">Send Reset Link</button>
        </form>
        <div class="links"><a href="login.php">Back to Login</a></div>
    </div>
<?php
$message = '';
$message_type = 'success';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    // In a real application, you would generate a token, save it, and send an email.
    // For this example, we just show a confirmation message for security.
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "If an account with that email exists, a password reset link has been sent.";
    } else {
        $message = "Please enter a valid email address.";
        $message_type = 'error';
    }
}
?>

</body>
</html>