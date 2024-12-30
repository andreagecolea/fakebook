<?php

session_start();
$conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');


$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <title>Search Results</title>
    <link rel="icon" href="https://uxwing.com/wp-content/themes/uxwing/download/user-interface/search-icon.png">
    <link rel="shortcut icon" href="https://uxwing.com/wp-content/themes/uxwing/download/user-interface/search-icon.png">
<style>
    
    * {
        margin: 0;
        padding: 0;
        font-family: "Poppins", sans-serif;
        box-sizing: border-box;
    }
    body {
        margin: 0;
        padding: 0;
        background-color: #f8f9fa;
        color: #212529;
    }

    header {
        background-color: #333;
        color: #fff;
        padding: 10px;
        text-align: center;
        width: 100%;
        position: fixed; 
        top: 0; 
        height: 65px;
        z-index: 1000;
    }
    .msg-btn{
        background: none;
        border: none;
        cursor: pointer;
        margin-right: 10px;
    }
    .bi-envelope-heart-fill{
        color: white;
        font-size: 20px;
    }
    .bi-envelope-heart-fill:hover{
        opacity: 80%;
    }
    .bi-person-circle{
        color: white;
        font-size: 20px;
        cursor: pointer;
    }
    .bi-person-circle:hover{
        opacity: 80%;
    }
    .profile-dropdown{
        text-decoration: none;
    }
    .profile:hover{
        opacity: 80%;
    }
    .profile-btn{
        border: none;
        background: none;
    }
    .profile-dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 190px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
        transform: translateX(-90%);
        margin-top: 5px;
        border-radius: 20px;
    }
    .dropdown-content a {
        padding: 12px 16px;
        display: block;
        text-decoration: none;
        color: #333;
        cursor: pointer;
        font-size: 15px;
        margin-left: 35px;
    }

    .dropdown-content a:hover {
        background-color: #f1f1f1;
        border-radius: 30px;
    }

    .dropdown-content i {
        left: 0;
        margin-left: 15px;
        font-size: 20px;
        position: absolute;
        color: black;
    }
    .search-container {
        position: absolute;
        top: 0;
        padding: 5px;
        margin-top: 10px;
        }
    .search-container input {
        border-radius: 30px;
    }
    .search-container i {
        color: white;
    }
    @media (max-width: 920px) {
        body{
            width: 100%;
        }
        header {
            height: 60px;
        }

        header h1 {
            font-size: 18px;
        }

        .img-header {
            display: none;
        }

        main {
            padding: 20px;
        }

        .post-area {
            height: 50px;
        }

        .search-container {
            margin-right: 15%;
        }

        .dropdown-content {
            left: -100%;
        }

        footer {
            height: 40px;
        }

        .textarea-container {
            margin-top: 10px;
        }



        .post-container {
            padding: 10px;
        }

        .profile-picture {
            width: 30px;
            height: 30px;
        }

        .username {
            font-size: 14px;
        }

        .timestamp {
            font-size: 8px;
        }

        .edit-dlt i,
        .edit-form button {
            font-size: 14px;
        }

        .char-count1 {
            top: 60px;
            margin-right: 10px;
        }

        .swiper-container {
            width: 100%;
            margin: 10px auto;
        }

        .swiper-pagination {
            bottom: 5px;
        }

        .msg-btn i,
        .profile-btn i,
        .bi-search {
            font-size: 20px;
        }

        .search-container input {
            width: 75%;
        }

        .search-container button {
            display: none;
        }

        .dropdown-content a {
            font-size: 14px;
            margin-left: 10px;
        }

        .end {
            font-size: 10px;
            margin-bottom: 10px;
        }

        footer {
            height: 30px;
        }
        .fakebook{
            display: none;
        }

    }
</style>
</head>
<body>
<header>
        <h1>
            <a href="home.php"><img src="https://usagif.com/wp-content/uploads/gifs/happy-cat-26.gif" width="50px" class="img-header"></a>
            <p class="fakebook">FakeBook</p>
        </h1>
        <div class="logout-container">
            <button class="msg-btn">
            <a href="messages.php"><i class="bi bi-envelope-heart-fill" title="Messages"></i></a>
            </button>
            <div class="profile-dropdown">
            <button class="profile-btn" onclick="toggleProfileDropdown()">
                <i class="bi bi-person-circle" title="More Options"></i>
            </button>
            <div class="dropdown-content">
                <a href="profile.php"><i class="bi bi-person"></i>View Profile</a>
                <a href="contact.php" onclick="contactUs()"><i class="bi bi-envelope"></i>Contact Us</a>
                <a href="#" onclick="confirmLogout()"><i class="bi bi-box-arrow-left"></i>Logout</a>
            </div>
        </div>
        </div>
        <div class="search-container">
        <form action="search.php" method="post">
            <input type="text" name="search" placeholder="Search user" required oninvalid="this.setCustomValidity('Username cannot be empty')" oninput="setCustomValidity('')">
            <button type="submit" name="submit"><i class="bi bi-search"></i></button>
        </form>
        </div>

    </header>
    <main>
        <h2><a href="home.php"><i class="bi bi-arrow-left-circle"></i></a> Search Results</h2>
        <?php if (isset($searchResults) && !empty($searchResults)): ?>
            <?php foreach ($searchResults as $user): ?>
                <ul>
                    <?php foreach ($user['posts'] as $post): ?>
                        <?php include 'post_template.php'; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        <?php else: ?>
            <?php include 'post_template.php'; ?>
        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
    <script>
        
    function confirmLogout() {
            Swal.fire({
                title: 'Are you sure you want to log out?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, log out'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.html';
                }
            });
        }

        
        function toggleProfileDropdown() {
            const dropdownContent = document.querySelector('.dropdown-content');
            const isVisible = window.getComputedStyle(dropdownContent).display === 'block';

            if (isVisible) {
                dropdownContent.style.display = 'none';
            } else {
                dropdownContent.style.display = 'block';
                document.addEventListener('click', handleOutsideClick);
            }
        }

        function handleOutsideClick(event) {
            const dropdownContent = document.querySelector('.dropdown-content');
            const profileBtn = document.querySelector('.profile-btn');

            if (!dropdownContent.contains(event.target) && !profileBtn.contains(event.target)) {
                dropdownContent.style.display = 'none';
                document.removeEventListener('click', handleOutsideClick);
            }
        }
    </script>
    
</body>
</html>
