<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_POST['postContent'])) {
        $user_id = $_SESSION['user_id']; 
        $content = $_POST['postContent'];

        $image_path = ''; 

        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image_name = $_FILES['image']['name'];
            $image_tmp = $_FILES['image']['tmp_name'];
            $image_path = 'img/' . $image_name;

            move_uploaded_file($image_tmp, $image_path);
        }

        $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $content, $image_path);

        $stmt->execute();

        $stmt->close();
        $conn->close();

        header("Location: home.php");
        exit();
    } elseif (isset($_POST['edited_content'], $_POST['post_id'])) {
        $user_id = $_SESSION['user_id'];
        $post_id = $_POST['post_id'];
        $edited_content = $_POST['edited_content'];

        $stmt = $conn->prepare("UPDATE posts SET content = ? WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param("sii", $edited_content, $post_id, $user_id);

        $stmt->execute();

        $stmt->close();
        $conn->close();

        echo "Post updated successfully";
        exit();
    }
}
?>
