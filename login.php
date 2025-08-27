<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tech NIG</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        <?php if (!empty($success_message)): ?>
            <p class="success"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <div class="links">
            <a href="forgot_password.php">Forgot Password?</a>
            <br>
            <a href="signup.php">Don't have an account? Sign Up</a>
        </div>
    </div>
<?php
include 'db.php';
session_start();

$success_message = ''; // Variable to hold success messages
if (isset($_GET['signup']) && $_GET['signup'] == 'success') {
    $success_message = "Account created successfully! Please log in.";
}

$error_message = ''; // Variable to hold error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error_message = "Email and password are required.";
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                if (password_verify($password, $row['password'])) {
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);

                    // store session data
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['email'] = $row['email'];

                    header("Location: dashboard.php");
                    exit();
                } else {
                    // Use a generic message to avoid user enumeration
                    $error_message = "Invalid email or password!";
                }
            } else {
                $error_message = "Invalid email or password!";
            }
            $stmt->close();
        } else {
            $error_message = "An error occurred. Please try again later.";
        }
    }
}
?>

</body>
</html>