<?php
include 'db.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        // Check if email already exists
        $sql_check = "SELECT id FROM users WHERE email = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error_message = "An account with this email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sss", $username, $email, $hashed_password);
                if ($stmt->execute()) {
                    header("Location: login.php?signup=success");
                    exit();
                } else {
                    $error_message = "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "An error occurred. Please try again later.";
            }
        }
        $stmt_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Tech NIG</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-wrapper">
        <div class="form-container">
            <h2>Sign Up</h2>
            <?php if (!empty($error_message)): ?>
                <p class="error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <form method="POST" action="signup.php">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Sign Up</button>
            </form>
            <div class="links">
                <a href="login.php">Already have an account? Login</a>
            </div>
        </div>
    </div>
</body>
</html>
