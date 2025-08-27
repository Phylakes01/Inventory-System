<?php
include 'db.php';
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $token = bin2hex(random_bytes(50));
    $expire = date("Y-m-d H:i:s", strtotime("+1 hour"));

    $sql = "UPDATE users SET reset_token=?, token_expire=? WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $token, $expire, $email);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $resetLink = "http://localhost/reset_password.php?token=$token";
        echo "Password reset link: <a href='$resetLink'>$resetLink</a>";
    } else {
        echo "Email not found!";
    }
}
?>

<form method="POST">
    Enter your email: <input type="email" name="email" required><br>
    <button type="submit">Send Reset Link</button>
</form>
