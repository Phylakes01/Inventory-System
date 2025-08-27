<?php
include 'db.php';
session_start();

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newPass = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password=?, reset_token=NULL, token_expire=NULL WHERE reset_token=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $newPass, $token);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo "Password updated! <a href='login.php'>Login</a>";
        } else {
            echo "Invalid or expired token!";
        }
    }
} else {
    echo "No token provided!";
}
?>

<form method="POST">
    New Password: <input type="password" name="password" required><br>
    <button type="submit">Reset Password</button>
</form>
