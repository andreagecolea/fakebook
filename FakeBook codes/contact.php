<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contact Us!</title>
    <link rel="icon" href="https://icons.veryicon.com/png/o/miscellaneous/template-4/telephone-contact-1.png">
    <link rel="shortcut icon" href="https://icons.veryicon.com/png/o/miscellaneous/template-4/telephone-contact-1.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
<style>
    * {
        margin: 0;
        padding: 0;
        font-family: "Poppins", sans-serif;
        box-sizing: border-box;
    }
    body {
        background-image: url("https://wallpapercave.com/wp/wp2757874.gif");
        background-size: cover; 
        background-position: center;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    .container {
        max-width: 400px;
        margin: 100px auto;
        padding: 25px;
        box-shadow: 0 0 2px #ccc; 
        border-radius: 30px;
        opacity: 90%;
        color: white;
        width: 100%;
    }
    .container h2{
        text-align: center;
        color: white;
        opacity: 90%;
    }
    form {
        display: flex;
        flex-direction: column;
    }

    input, textarea {
    margin-bottom: 15px;
        padding: 8px;
        border: none;
        border-radius: 3px;
        background: transparent;
        color: white;
        box-shadow: 0 0 1px #ccc; /* Adjust this value to make the "border" thinner */
    }
    input[type="submit"] {
        background-color: #333;
        color: white;
        cursor: pointer;
        border-radius: 30px;
    }

    input[type="submit"]:hover {
        opacity: 80%;
    }
    textarea{
        resize: none;
        height: 100px;
    }
    .line {
        width: 100%;
        height: 1px;
        opacity: 50%;
        background-color: #afacac;
        margin: 25px 0;
    }
    .back{
        text-align: center;
        font-size: 12px;
        color: white;
        opacity: 90%;
        text-decoration: none;
    }
    .back:hover{
        text-decoration: underline;
    }
    .links {
        text-align: center;
        margin-top: 20px;
        opacity: 80%;
    }

    .links .line1 {
        width: 50%;
        height: 1px;
        background-color: #afacac;
        margin: 5px auto;
    }

    .links a {
        font-size: 25px;
        cursor: pointer;
        color: white;
        text-decoration: none;
        margin-right: 5px;
    }

    .links a:hover {
        opacity: 50%;
    }
    @media (max-width: 920px){
        body{
            padding: 20px;
        }
    }
</style>
</head>
<body>
<div class="container">
    <h2>Contact Us</h2>
    <div class="line"></div>
    <form action="send_email.php" method="post">
        <input placeholder="Name:" type="text" id="name" name="name" required>

        <input placeholder="Email:" type="email" id="email" name="email" required>

        <textarea placeholder="Message:" id="message" name="message" required></textarea>

        <input type="submit" value="Send Message">
        <a href="home.php" class="back">← Go back to homepage</a>
    </form>
    <div class="links">
    <div class="line1"></div>
    <a href="https://www.facebook.com/profile.php?id=61554880315987"><i class="bi bi-facebook"></i></a>
    <a href="mailto:gecowebdev@gmail.com"><i class="bi bi-envelope-at-fill"></i></a>
    </div>
</div>

</body>
</html>