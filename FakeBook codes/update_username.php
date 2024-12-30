<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    echo "User ID not set in session.";
    header("Location: index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = filter_input(INPUT_POST, 'new_username', FILTER_SANITIZE_STRING);

    if ($newUsername) {
        $conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $checkQuery = "SELECT user_id FROM users WHERE username = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("s", $newUsername);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        var_dump($checkResult);

        if ($checkResult === false) {
            echo "Error executing the check query: " . $conn->error;
            exit();
        }

        if ($checkResult->num_rows > 0) {
            echo "Username already exists.";
            header("Location: profile.php?success=false");
            exit();
        }

        $updateQuery = "UPDATE users SET username = ? WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("si", $newUsername, $userId);
        $updateResult = $updateStmt->execute();

        var_dump($updateResult);

        if ($updateResult) {
            echo "Username updated successfully.";
            header("Location: profile.php?success=true");
            exit();
        } else {
            echo "Update failed: " . $updateStmt->error;
            header("Location: profile.php?success=false");
            exit();
        }
    } else {
        echo "Invalid new username.";
        header("Location: profile.php?success=false");
        exit();
    }
} else {
    header("Location: index.html");
    exit();
}
?>
