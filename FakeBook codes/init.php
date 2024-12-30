<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    header("Location: index.html");
    exit();
}
$conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT posts.*, users.username FROM posts 
          INNER JOIN users ON posts.user_id = users.user_id 
          ORDER BY posts.created_at DESC";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $posts = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $posts = array();
}

$conn->close();
?>