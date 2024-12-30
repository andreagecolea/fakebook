<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message']) && isset($_POST['receiver_id'])) {
    $senderId = $_SESSION['user_id'];
    $receiverId = $_POST['receiver_id'];
    $messageContent = $_POST['message'];

    if (empty($messageContent)) {
        echo json_encode(['success' => false, 'message' => 'Message content cannot be empty.']);
        exit();
    }

    $conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "INSERT INTO messages (sender_id, receiver_id, message_content, sent_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $senderId, $receiverId, $messageContent);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    echo json_encode(['success' => true, 'message' => 'Message sent successfully.']);
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}
?>
