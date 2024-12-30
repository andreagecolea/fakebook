<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient_email = "gecowebdev@gmail.com";
    $subject = "New Contact Form Submission";

    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    $email_message = "Name: $name\n";
    $email_message .= "Email: $email\n";
    $email_message .= "Message:\n$message";

    $headers = "From: $email";

    mail($recipient_email, $subject, $email_message, $headers);

    header("Location: thank_you.php");
    exit();
}

?>
