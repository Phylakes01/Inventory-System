<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Tech NIG</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Create Account</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form method="POST" action="signup.php" novalidate>
            <div class="form-group"><label for="username">Username:</label><input type="text" id="username" name="username" required></div>
            <div class="form-group"><label for="email">Email:</label><input type="email" id="email" name="email" required></div>
            <div class="form-group"><label for="password">Password:</label><input type="password" id="password" name="password" minlength="8" required></div>
            <div class="form-group"><label for="confirm_password">Confirm Password:</label><input type="password" id="confirm_password" name="confirm_password" required></div>
            <button type="submit">Sign Up</button>
        </form>
        <div class="links">
         
            <a href="login.php">Already have an account? Login</a>
        </div>
    </div>
<?php
include 'db.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if email already exists
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "An account with this email already exists.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $sql_insert = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt_insert->execute()) {
                // Redirect to login page with a success message
                header("Location: login.php?signup=success");
                exit();
            } else {
                $error_message = "Error: Could not create account. Please try again.";
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }
}
?>

</body>
</html>