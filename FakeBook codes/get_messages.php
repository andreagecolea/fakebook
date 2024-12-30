<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$contact_id = $_POST['contact_id'];
$messages = getMessages($contact_id);

if ($messages === false) {
    echo json_encode(['error' => 'Failed to fetch messages']);
} else {
    echo json_encode(['success' => true, 'messages' => $messages]);
}

function getMessages($contact_id) {
    $conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');

    if ($conn->connect_error) {
        return false;
    }

    $query = "SELECT m.sender_id, m.message_content, u.username 
              FROM messages m
              JOIN users u ON m.sender_id = u.user_id
              WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?) 
              ORDER BY m.sent_at";

    $stmt = $conn->prepare($query);
    $user_id = $_SESSION['user_id'];
    $stmt->bind_param("iiii", $user_id, $contact_id, $contact_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];

    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    $stmt->close();
    $conn->close();

    return $messages;
}
?>
