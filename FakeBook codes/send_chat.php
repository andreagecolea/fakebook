<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

if (!isset($_POST['contact_id']) || !isset($_POST['message_text'])) {
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['contact_id'];
$message_content = $_POST['message_text'];

$conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

$query = "INSERT INTO messages (sender_id, receiver_id, message_content, sent_at) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param("iis", $sender_id, $receiver_id, $message_content);
$stmt->execute();
$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
exit();
?>
