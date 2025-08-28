<?php
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user settings
$settings_query = "SELECT * FROM user_settings WHERE user_id = ?";
$stmt = $conn->prepare($settings_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$settings = $result->fetch_assoc();

// If no settings found, create a default entry
if (!$settings) {
    $insert_default_settings = "INSERT INTO user_settings (user_id) VALUES (?)";
    $stmt = $conn->prepare($insert_default_settings);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    // Refetch settings
    $stmt = $conn->prepare($settings_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $settings = $result->fetch_assoc();
}
?>