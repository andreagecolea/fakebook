<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    header("Location: index.html");
    exit();
}

$defaultImageSource = 'img/default_profile_pic.webp';

$conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$updateQuery = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bind_param("si", $defaultImageSource, $userId);
$updateStmt->execute();

if ($updateStmt->error) {
    header("Location: profile.php?success=false");
} else {
    header("Location: profile.php?success=true");
}

$conn->close();
?>
