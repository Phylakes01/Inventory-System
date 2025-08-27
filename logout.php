<?php
session_start();
session_destroy();
echo "Logged out. <a href='login.php'>Login again</a>";
?>
