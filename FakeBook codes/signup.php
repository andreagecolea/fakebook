<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['signup_username'];
    $password = $_POST['signup_password'];

    $conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $checkQuery = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        echo "Error: Username already taken.";
    } else {
        $insertQuery = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

        if ($conn->query($insertQuery) === TRUE) {
            echo "Account created successfully!";
        } else {
            echo "Error: " . $insertQuery . "<br>" . $conn->error;
        }
    }

    $conn->close();
}
?>
