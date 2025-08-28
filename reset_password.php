<?php
include 'db.php';

$error_message = '';
$success_message = '';

// In a real application, you would get the token from the URL
// For example: reset_password.php?token=some_token
$token = $_GET['token'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($password) || empty($password_confirm)) {
        $error_message = "Please enter and confirm your new password.";
    } elseif ($password !== $password_confirm) {
        $error_message = "Passwords do not match.";
    } else {
        // In a real application, you would validate the token and update the user's password
        $success_message = "Your password has been reset successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Tech NIG</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-wrapper">
        <div class="form-container">
            <h2>Reset Password</h2>
            <?php if (!empty($error_message)): ?>
                <p class="error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <p class="success"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <form method="POST" action="reset_password.php?token=<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="form-group">
                    <label for="password">New Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="password_confirm">Confirm New Password:</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>
                <button type="submit">Reset Password</button>
            </form>
            <div class="links">
                <a href="login.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
