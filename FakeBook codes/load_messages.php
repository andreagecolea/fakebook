<?php
session_start();

if (isset($_GET['receiver_id'])) {
    $receiverId = $_GET['receiver_id'];
    $userId = $_SESSION['user_id'];

    $conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT messages.*, users.username, users.profile_picture 
              FROM messages
              JOIN users ON messages.sender_id = users.user_id
              WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) 
              ORDER BY sent_at";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $userId, $receiverId, $receiverId, $userId);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $messages = $result->fetch_all(MYSQLI_ASSOC);
        $response = ['success' => true, 'messages' => $messages];
        echo json_encode($response);
    } else {
        $response = ['success' => false];
        echo json_encode($response);
    }

    $stmt->close();
    $conn->close();
} else {
    $response = ['success' => false];
    echo json_encode($response);
}
?>
