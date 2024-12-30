<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    header("Location: index.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['new_profile_picture'])) {
    $targetDir = 'img/';
    $originalFileName = basename($_FILES['new_profile_picture']['name']);
    $imageFileType = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES['new_profile_picture']['tmp_name']);
    if ($check === false) {
        echo "File is not an image.";
        exit();
    }

    $newFileName = generateUniqueFileName($targetDir, $originalFileName);

    $targetFile = $targetDir . $newFileName;

    if ($_FILES['new_profile_picture']['size'] > 10000000) {
        echo "File is too large.";
        exit();
    }

    $allowedFormats = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($imageFileType, $allowedFormats)) {
        echo "Invalid file format.";
        exit();
    }

    if (move_uploaded_file($_FILES['new_profile_picture']['tmp_name'], $targetFile)) {
        $conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $updateQuery = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("si", $targetFile, $userId);
        $updateStmt->execute();

        if ($updateStmt->affected_rows > 0) {
            header("Location: profile.php?success=true");
            exit();
        } else {
            echo "Failed to update profile picture in the database.";
        }

        $conn->close();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
} else {
    echo "Invalid request.";
}

function generateUniqueFileName($targetDir, $originalFileName) {
    $newFileName = $originalFileName;
    $counter = 1;

    while (file_exists($targetDir . $newFileName)) {
        $filenameWithoutExt = pathinfo($originalFileName, PATHINFO_FILENAME);
        $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);

        $newFileName = $filenameWithoutExt . '_' . $counter . '.' . $extension;
        $counter++;
    }

    return $newFileName;
}
?>
