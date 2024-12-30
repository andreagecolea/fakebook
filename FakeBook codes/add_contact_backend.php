<?php
session_start();

if (isset($_GET['user_id'])) {
    $contactUserId = $_GET['user_id'];
    $loggedInUserId = $_SESSION['user_id'];

    $conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($contactUserId != $loggedInUserId) {
        $checkContactQuery = $conn->prepare("SELECT * FROM contacts WHERE user_id = ? AND contact_id = ?");
        $checkContactQuery->bind_param("ii", $loggedInUserId, $contactUserId);
        $checkContactQuery->execute();
        $result = $checkContactQuery->get_result();

        if ($result->num_rows == 0) {
            $addContactQuery = $conn->prepare("INSERT INTO contacts (user_id, contact_id) VALUES (?, ?)");
            $addContactQuery->bind_param("ii", $loggedInUserId, $contactUserId);
            $addContactResult = $addContactQuery->execute();

            if ($addContactResult) {
                $success = true;

                $getContactNameQuery = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
                $getContactNameQuery->bind_param("i", $contactUserId);
                $getContactNameQuery->execute();
                $contactNameResult = $getContactNameQuery->get_result();

                if ($contactNameResult && $contactNameResult->num_rows > 0) {
                    $contactData = $contactNameResult->fetch_assoc();
                    $data = [
                        'contactName' => $contactData['username'],
                    ];
                } else {
                    $success = false;
                    $data = [];
                }
            } else {
                $success = false;
                $data = [];
            }
        } else {
            $success = false;
            $data = [];
        }

        $checkContactQuery->close();
        $addContactQuery->close();
        $getContactNameQuery->close();
    } else {
        $success = false;
        $data = [];
    }

    $conn->close();
} else {
    $success = false;
    $data = [];
}

header('Content-Type: application/json');
echo json_encode(['success' => $success, 'data' => $data]);
?>
