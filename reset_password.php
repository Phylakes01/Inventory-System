<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Tech NIG</title>
    <link rel="stylesheet" href="src/styles/system.css">
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <h1>Reset Your Password</h1>
            
            <?php if (!empty($error_message)): ?><p class="error-message"><?php echo $error_message; ?></p><?php endif; ?>
            <?php if (!empty($success_message)): ?><p class="success-message"><?php echo $success_message; ?></p><?php endif; ?>
            
            <?php if (empty($success_message) && empty($error_message)): ?>
            <form method="POST" action="reset_password.php?token=<?php echo $token; ?>">
                <div class="form-group"><label for="password">New Password</label><input type="password" id="password" name="password" required></div>
                <div class="form-group"><label for="confirm_password">Confirm New Password</label><input type="password" id="confirm_password" name="confirm_password" required></div>
                <button type="submit">Reset Password</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
<?php
// This is a placeholder for reset password logic.
// In a real application, you would validate the token from the URL.
$token = isset($_GET['token']) ? htmlspecialchars($_GET['token']) : '';
$error_message = '';
$success_message = '';

if (empty($token)) {
    $error_message = "Invalid or missing reset token.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error_message)) {
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $error_message = "Passwords do not match!";
    } else {
        // Here you would update the password in the database
        $success_message = "Your password has been reset successfully! You can now <a href='login.php'>login</a>.";
    }
}
?>

</body>
</html>