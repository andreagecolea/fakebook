<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
</head>
<style>
     * {
        margin: 0;
        padding: 0;
        font-family: "Poppins", sans-serif;
        box-sizing: border-box;
        text-align: center;
    }
    body{
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    .container{
        border: 1px dotted black;
        border-radius: 30px;
        padding: 60px 50px;
        height: 300px;
    }
    .back{
        text-decoration: none;
        color: blue;
    }
    .container a:hover{
        text-decoration: underline;
    }
    .links{
        margin-top: 70px;
        font-size: 30px;
        cursor: pointer;
    }
    .links a{
        color: #333;
        text-decoration: none;
    }
    .links:after{
        color: #333;
    }
    .line {
                width: 100%;
                height: 1px;
                background-color: #afacac;
                margin: 10px 0;
            }
    .line1 {
                width: 50%;
                height: 1px;
                background-color: #afacac;
                margin: auto;
            }
    @media (max-width: 920px){
        body{
            padding: 20px;
        }
    }
</style>
<body>

<div class="container">
    <h2>Thank You!</h2>
    <div class="line"></div>
    <p>Your message has been sent successfully. We will get back to you soon.</p>
    <p><a href="contact.php" class="back">← Go back to the contact form</a></p>
    <div class="links">
    <div class="line1"></div>
    <a href="https://www.facebook.com/profile.php?id=61554880315987"><i class="bi bi-facebook"></i></a>
    <a href="mailto:gecowebdev@gmail.com"><i class="bi bi-envelope-at-fill"></i></a>
    </div>
</div>

</body>
</html>
