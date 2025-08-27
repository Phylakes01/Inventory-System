<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
<p>Your email: <?php echo $_SESSION['email']; ?></p>
<a href="logout.php">Logout</a>
