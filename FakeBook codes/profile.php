<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    header("Location: index.html");
    exit();
}

$defaultImageSource = 'img/default_profile_pic.webp'; 
$conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userQuery = "SELECT * FROM users WHERE user_id = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows > 0) {
    $user = $userResult->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

$postQuery = "SELECT posts.*, users.username, users.profile_picture, GROUP_CONCAT(post_images.image_path) AS image_paths
              FROM posts
              INNER JOIN users ON posts.user_id = users.user_id
              LEFT JOIN post_images ON posts.post_id = post_images.post_id
              WHERE posts.user_id = ?
              GROUP BY posts.post_id
              ORDER BY posts.created_at DESC";

$postStmt = $conn->prepare($postQuery);
$postStmt->bind_param("i", $userId);
$postStmt->execute();
$postResult = $postStmt->get_result();

if ($postResult->num_rows > 0) {
    $posts = array();
    while ($row = $postResult->fetch_assoc()) {
        $posts[] = $row;
    }
} else {
    $posts = array();
}

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
    <title>User Profile</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/7605/7605078.png">
    <link rel="shortcut icon" href="https://assets.materialup.com/uploads/b6c33467-82c3-442c-a2dc-c089bbff9fa1/preview.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    <?php foreach ($posts as $post): ?>
        <?php if (!empty($post['image_paths'])): ?>
            <?php
                $imagePaths = explode(',', $post['image_paths']);
                $totalImages = count($imagePaths);
            ?>

            <?php if ($totalImages > 1): ?>
                var swiper_<?php echo $post['post_id']; ?> = new Swiper('.swiper-container-<?php echo $post['post_id']; ?>', {
                    effect: 'cards',
                    grabCursor: true,
                    centeredSlides: true,
                    slidesPerView: 'auto',
                    spaceBetween: 20,
                    pagination: {
                        el: '.swiper-pagination-<?php echo $post['post_id']; ?>',
                        clickable: true,
                        renderBullet: function (index, className) {
                            return '<span class="' + className + '">' + '</span>';
                        },
                    },
                });

                if (<?php echo $totalImages; ?> <= 1) {
                    swiper_<?php echo $post['post_id']; ?>.pagination.el.style.display = 'none';
                    swiper_<?php echo $post['post_id']; ?>.navigation.nextEl.style.display = 'none';
                    swiper_<?php echo $post['post_id']; ?>.navigation.prevEl.style.display = 'none';
                }
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
});
</script>
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

    header h1 {
        margin: 0;
        display: flex;
        align-items: center;
        color: white;
        font-size: 25px;
    }

    .img-header {
        margin-right: 10px;
        border-radius: 50%;
        cursor: pointer;
    }

    .logout-container {
        position: absolute;
        bottom: 20px;
        right: 20px;
        font-size: 12px;
    }
    .btnl {
        text-decoration: none;
        color: white;
        font-size: 20px;
        background-color: none;
        padding: 0;
        border-radius: 5px;
    }

    .btnl:hover {
        opacity: 90%;
    }

    main {
        max-width: 600px;
        width: 100%;
        height: 260px;
        padding: 20px;
        box-sizing: border-box;
        margin: 80px auto 0;
        background-color: #DFDFDF;
        border-radius: 30px;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    .button {
        padding: 3px;
        background-color: #333;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: 70px;
        font-size: 12px;
        transition: background-color 0.3s;
        align-self: flex-end;
    }
    .textarea-container {
        position: relative;
    }

    .post-area {
        resize: none;
        height: 90px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 10px;
        width: 100%;
        box-sizing: border-box;
        margin: 0 auto;
    }
    .char-count {
        position: absolute;
        bottom: 5px;
        right: 5px;
        color: #777;
        font-size: 11px;
        margin-bottom: 7px;
    }

    .button:hover {
        background-color: #494949;
    }
    .savebtn {
        padding: 5px;
        background-color: #333;
        color: #fff;
        font-size: 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
        width: 65px;
    }

    .savebtn:hover {
        background-color: #494949;
    }
    .cancelbtn {
        padding: 5px;
        width: 65px;
        background-color: #333;
        font-size: 12px;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .cancelbtn:hover {
        background-color: #494949;
    }
    .post-container {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 15px;
        margin-bottom: 20px;
        padding: 15px;
        margin-top: 20px;
        position: relative;
        padding-bottom: 30px;
    }
    .user-profile {
        text-align: center;
        margin-top: 20px; 
    }
    .profile-picture {
        max-width: 150px;
        width: 120px;
        height: 115px;
        border-radius: 50%;
        margin: auto;
        display: flex;
        cursor: pointer;
    }
    .profile-picture:hover{
        opacity: 80%;
    }
    .content {
        text-align: left;
        margin-bottom: 12px;
        margin-top: 10px;  
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .timestamp {
        position: absolute;
        right: 5px;
        bottom: 0;
        margin-bottom: 5px;
        margin-right: 15px;
        color: #777;
        font-size: 10px;
    }
    .line {
        width: 100%;
        height: 1px;
        background-color: #afacac;
        margin: 10px 0;
    }
    #trash{
        font-size: 15px;
        cursor: pointer;
    }
    .trash:hover{
        opacity: 60%;
    }
    .dltbtn{
        border: none;
        background-color: white;
    }
    .delete-btn {
        position: absolute;
        top: 0;
        right: 0;
        margin-top: 25px;
        margin-right: 15px;
    }
    .bi-pencil-square{
        font-size: 15px;
        cursor: pointer;
    }
    .bi-pencil-square:hover{
        opacity: 80%;
    }
    .file-label {
        padding: 5px;
        color: #333;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        position: absolute;
        margin-top: 60px;
        margin-left: 5px;
    }

    .file-label:hover {
        opacity: 80%;
    }

    .file-label i{
        padding-right: 5px;
    }

    input[type="file"] {
        display: none;
    }
    .search-container {
        position: absolute;
        top: 0;
        right: 7%;
        padding: 5px;
        margin-top: 10px;
    }
    .search-container input {
        padding: 5px;
        width: 85%;
        border-radius: 20px;
        font-size: 12px;
        padding-left: 15px;
    }
    .search-container button {
        position: absolute;
        cursor: pointer;
        border: none;
        text-decoration: none;
        background: none;
        right: 0;
        margin-right: 7px;
        margin-top: 2px;
    }
    .search-container i {
        font-size: 18px;
        color: white;
    }
    .swiper-container {
        width: 100%;
        margin: auto;
    }

    .swiper-slide {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background-color: transparent;
    }

    .swiper-image {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        border-radius: 20px;
        margin-bottom: 10px;
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
        transform: translateX(-80%);
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
        font-size: 17px;
        position: absolute;
        color: black;
    }
    .bi-pencil-square{
        margin-left: 10px;
        font-size: 16px;
    }
    .edit-container{
        margin: auto;
        font-size: 12px;
        margin-top: 10px;
    }
    .edit-container input{
        padding: 2px;
        font-size: 12px;
    }
    
    .edit-container button{
        border: none;
        padding: 2px;
        width: 70px;
        font-size: 11px;
        border-radius: 20px;
        background-color: #333;
        color: white;
    }
    .edit-container button:hover{
        opacity: 80%;
    }
    .edit-btn{
        border: none;
        background-color: #333;
        color: white;
        padding: 5px;
        font-size: 12px;
        width: 100px;
        border-radius: 5px;
        cursor: pointer;
        margin-bottom: 5px;
    }
    .edit-btn:hover{
        opacity: 80%;
    }
    .update-btn{
        width: 100px;
    }
    .bi-camera{
        margin-right: 8px;
    }
    .remove-btn{
        border: none;
        background: none;
        cursor: pointer;
        position: absolute;
        font-size: 22px;
        margin-left: 25%;
        margin-top: 1%;
    }
    .back-btn{
        margin-top: 10px;
        margin-left: 50px;
        position: absolute;
        font-size: 35px;
    }
    .back-btn i{
        text-decoration: none;
        color: #333;
    }
    .back-btn:hover{
        opacity: 80%;
    }
    .post-container {
        max-width: 600px;
        width: 100%;
        padding: 20px;
        box-sizing: border-box;
        margin: 30px auto 0;
        background-color: #fff;
        border-radius: 15px;
    }
    .user-info {
        display: flex;
        align-items: center;
    }


    .username {
        font-weight: bold;
        margin-bottom: 8px;
    }

    .content {
        text-align: left;
        margin-bottom: 12px;
        margin-top: 10px;  
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .timestamp {
        position: absolute;
        right: 5px;
        bottom: 0;
        margin-bottom: 5px;
        margin-right: 15px;
        color: #777;
        font-size: 10px;
    }
    .line {
        width: 100%;
        height: 1px;
        background-color: #afacac;
        margin: 10px 0;
    }
    #trash{
        font-size: 15px;
        cursor: pointer;
    }
    .trash:hover{
        opacity: 60%;
    }
    .dltbtn{
        border: none;
        background-color: white;
    }
    .delete-btn {
        position: absolute;
        top: 0;
        right: 0;
        margin-top: 25px;
        margin-right: 15px;
    }
    .bi-pencil-square{
        font-size: 15px;
        cursor: pointer;
    }
    .bi-pencil-square:hover{
        opacity: 80%;
    }
    .profile-picture2{
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-right: 10px;
    }
    .char-count1 {
            position: absolute;
            right: 0;
            margin-right: 30px;
            top: 140px;
            color: #777;
            font-size: 11px;
        }
    .end{
        text-align: center;
        font-size: 12px;
        margin-top: 20px;
        color: #333;
    }
    .no_result{
        text-align: center;
        margin-top: 70px;
        font-weight: bold;
    }
    .no_result p{
        font-size: 13px;
    }
    .no_result i{
        font-size: 30px;
    }
    .back-btn1{
        display: none;
    }
    #FullImageView{
            display: none;
            position: absolute;
            top:0;
            left:0;
            width: 100%;
            height: 100vh;
            background-color: rgba(0, 0, 0, .9);
            text-align: center;
            z-index: 1000;
        }
        #FullImage{
            padding: 20px;
            max-width: 90%;
            max-height: 90%;
            margin-top: 3%;
        }
        #FullImageView i{
            position:absolute;
            top: 12px;
            right: 12px;
            font-size: 25px;
            color: white;
            cursor: pointer;
            opacity: 100%;
        }
        #CloseButton:hover{
            opacity: 80%;
        }
        .no-scroll {
            overflow: hidden;
        }
    @media (max-width: 921px) {
    body {
        width: 100%;
        }

        .search-container {
            margin-right: 15%;
        }
        .search-container i{
            display: none;
        }
        .fakebook{
            display: none;
        }
        .back-btn{
            display: none;
        }
        .back-btn1{
            display: flex;
            color: #333;
            font-size: 30px;
            position: absolute;
            text-decoration: none;
        }
        .new{
            display: none;
        }
        .swiper-container{
            width: 80%;
        }
        main{
            width: 350px;
        }
        .post-container{
            width: 350px;
            font-size: 13px;
        }
        .img-header {
            display: none;
        }
        .edit-container input{
            width: 150px;
        }
        #FullImage{
            margin-top: 10%;
        }
        .bi-x-circle{
            color: #333;
        }
    }
    @media (max-width: 1722px){
        .bi-arrow-left-circle{
            color: #333;
        }
        .bi-x-circle{
            color: #333;
        }
        .bi-trash-fill{
            color: #333;
        }
    }
    @media (max-width: 1330px){
        .remove-btn{
            margin-left: 200px;
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
    <div class="back-btn">
        <a href="home.php">
            <i class="bi bi-arrow-left-circle" title="Home"></i>
        </a>
    </div>

    <main>
    <div class="back-btn1">
        <a href="home.php">
            <i class="bi bi-arrow-left-circle" title="Home"></i>
        </a>
    </div>
    <form action="remove_profile_picture.php" method="post">
        <button class="remove-btn" title="Remove Profile" onclick="removeProfile()" <?php echo ($user['profile_picture'] === $defaultImageSource) ? 'hidden' : ''; ?>><i class="bi bi-x-circle"></i></button>
    </form>

        <div class="user-profile">
            <img id="profilePicture" src="<?php echo $user['profile_picture']; ?>" alt="Profile Picture" class="profile-picture" onclick="FullView(this.src)" title="View Photo"><br>
            <button class="edit-btn" title="Upload Profile Picture"><i class="bi bi-camera"></i>Edit Photo</button>
            <h2 id="username"><?php echo $user['username']; ?><i class="bi bi-pencil-square" onclick="toggleEditUsername()"></i></h2>
        </div>
        <form action="update_username.php" method="post">
        <div id="editUsernameForm" style="display: none;" class="edit-container">
            <label for="newUsername" class="new">New Username:</label>
            <input type="text" id="new_username" name="new_username" required oninvalid="this.setCustomValidity('Username cannot be empty')" oninput="setCustomValidity('')">
            <button onclick="saveEditUsername()">Save</button>
            <button onclick="cancelEditUsername()">Cancel</button>
        </div>
        </form>
        <form action="update_profile.php" method="post" enctype="multipart/form-data" id="profileForm">
            <input type="file" id="new_profile_picture" name="new_profile_picture" style="display: none;" accept="image/*" onchange="submitProfileForm()">
            <label for="new_profile_picture" class="edit-btn" style="display: none;">Edit Photo</label>
        </form>
    </main>
    <?php if (!empty($posts)): ?>
    <?php foreach ($posts as $post):

        $isAuthor = ($post['user_id'] == $_SESSION['user_id']);
        $swiperContainerClass = 'swiper-container-' . $post['post_id'];
        ?>
        <div class="post-container" data-post-id="<?php echo $post['post_id']; ?>">
                <div class="user-info">
                <img
                src=<?php echo $user['profile_picture']; ?>
                alt="Profile Picture" class="profile-picture2">
                <p class="username"><strong><?php echo $user['username']; ?></strong></p>
                <p class="timestamp"><?php echo $post['created_at']; ?></p>
            </div>
            <div class="line"></div>
            <div class="edit-dlt" style="position: absolute; top: 0; right: 0; margin-top: 20px; margin-right: 15px;">
            <?php
                $isAuthor = ($userId == $post['user_id']);
            ?>

            <?php if (isset($isAuthor) && $isAuthor): ?>
                <i class="bi bi-pencil-square" title="Edit Post" onclick="toggleEdit(<?php echo $post['post_id']; ?>)"></i>
                <button class="dltbtn" type="button" onclick="confirmDelete(<?php echo $post['post_id']; ?>)">
                    <i id="trash" class="bi bi-trash-fill trash" title="Delete Post"></i>
                </button>
            <?php endif; ?>
            </div>
            <div class="edit-form" style="display: none;">
                <textarea class="post-area" name="editContent" id="editContent_<?php echo $post['post_id']; ?>" maxlength="255" oninput="updateCharCount(this)" required><?php echo $post['content']; ?></textarea>
                <div class="char-count1">0/255</div>
                <button type="button" class="savebtn" onclick="saveEdit(<?php echo $post['post_id']; ?>)">Save</button>
                <button type="button" class="cancelbtn" onclick="cancelEdit(<?php echo $post['post_id']; ?>)">Cancel</button>
            </div>
            <p class="content" id="postContent_<?php echo $post['post_id']; ?>" onclick="toggleEdit(<?php echo $post['post_id']; ?>)">
            <?php echo $post['content']; ?>
            </p>
            
    
            <?php if (!empty($post['image_paths'])): ?>
            <div class="swiper-container swiper-container-<?php echo $post['post_id']; ?>">
                <div class="swiper-wrapper">
                    <?php foreach (explode(',', $post['image_paths']) as $imagePath): ?>
                        <div class="swiper-slide">
                            <img src="<?php echo $imagePath; ?>" alt="Post Image" class="swiper-image" onclick="FullView(this.src)">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination swiper-pagination-<?php echo $post['post_id']; ?>"></div>
            </div>
            <?php endif; ?>
        </div>

    <?php endforeach; ?>
    <div class="end">
        <p>-- Nothing to see here. --</p>
    </div>
<?php else: ?>
    <div class="no_result">
    <i class="bi bi-emoji-frown"></i>
        <p>No posts yet</p>
    </div>
<?php endif; ?>

<div id="FullImageView">
    <img id="FullImage"/>
    <span id="CloseButton" onclick="CloseFullView()" title="Close"><i class="bi bi-x-circle"></i></span>
</div>
    <script type="text/javascript">
    function FullView(ImgLink){
        document.getElementById("FullImage").src = ImgLink;
    document.getElementById("FullImageView").style.display = "block";

    var scrollOffset = window.scrollY || window.pageYOffset || document.body.scrollTop || document.documentElement.scrollTop;

    document.getElementById("FullImageView").style.top = scrollOffset + "px";
    
    document.body.classList.add("no-scroll");
    }
    function CloseFullView(){
        document.getElementById("FullImageView").style.display = "none";
        document.body.classList.remove("no-scroll");
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
    
        const urlParams = new URLSearchParams(window.location.search);
        const successParam = urlParams.get('success');

        if (successParam !== null) {
            if (successParam === 'true') {
                Swal.fire({
                    icon: 'success',
                    title: 'Profile Updated Successfully!',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else if (successParam === 'false') {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Update Profile',
                    text: 'Username not available.',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        }
    });

</script>
<script>
    function removeProfile() {
    var defaultImageSource = 'img/default_profile_pic.webp';

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'remove_profile_picture.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        var response = JSON.parse(xhr.responseText);

        if (response.success) {
            document.getElementById('profilePicture').src = defaultImageSource;
            alert(response.message);
        } else {
            alert('Failed to remove profile picture. Please try again.');
        }
    };

    xhr.send();
}


</script>

    <script>
        function toggleEdit(postId) {
        const postContent = document.getElementById(`postContent_${postId}`);
        const editForm = document.querySelector(`.post-container[data-post-id="${postId}"] .edit-form`);
        const isEditing = editForm.style.display === 'block';

        if (!isEditing) {
            editForm.style.display = 'block';
            postContent.style.display = 'none';
        } else {
            editForm.style.display = 'none';
            postContent.style.display = 'block';
        }
    }
    function updateCharCount(textarea) {
            const maxLength = textarea.getAttribute('maxlength');
            const currentLength = textarea.value.length;
            const charCountElement = textarea.nextElementSibling;
            charCountElement.textContent = `${currentLength}/${maxLength}`;
        }
    function saveEdit(postId) {
        const editedContent = document.getElementById(`editContent_${postId}`).value;
        
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "update_post.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById(`postContent_${postId}`).textContent = editedContent;
                toggleEdit(postId);
            }
        };
        xhr.send("post_id=" + postId + "&edited_content=" + encodeURIComponent(editedContent));
    }

    function cancelEdit(postId) {
        toggleEdit(postId);
    }
        function toggleEditUsername() {
        const usernameElement = document.getElementById('username');
        const editForm = document.getElementById('editUsernameForm');
        const isEditing = editForm.style.display === 'block';

        if (!isEditing) {
            editForm.style.display = 'block';
            usernameElement.style.display = 'none';
        } else {
            editForm.style.display = 'none';
            usernameElement.style.display = 'block';
        }
    }
        document.addEventListener('DOMContentLoaded', function () {
            const profileForm = document.getElementById('profileForm');
            const fileInput = document.getElementById('new_profile_picture');

            fileInput.addEventListener('change', function () {
                profileForm.submit();
            });

            const editBtn = document.querySelector('.edit-btn');
            
            editBtn.addEventListener('click', function () {
                fileInput.click();
            });
        });



    function cancelEditUsername() {
        toggleEditUsername();
    }
        function toggleEdit(postId) {
        const postContent = document.getElementById(`postContent_${postId}`);
        const editForm = document.querySelector(`.post-container[data-post-id="${postId}"] .edit-form`);
        const isEditing = editForm.style.display === 'block';

        if (!isEditing) {
            editForm.style.display = 'block';
            postContent.style.display = 'none';
        } else {
            editForm.style.display = 'none';
            postContent.style.display = 'block';
        }
    }

    function saveEdit(postId) {
        const editedContent = document.getElementById(`editContent_${postId}`).value;
        
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "update_post.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById(`postContent_${postId}`).textContent = editedContent;
                toggleEdit(postId);
            }
        };
        xhr.send("post_id=" + postId + "&edited_content=" + encodeURIComponent(editedContent));
    }

    function cancelEdit(postId) {
        toggleEdit(postId);
    }
    function confirmDelete(postId) {
    Swal.fire({
        title: 'Are you sure you want to delete this post?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it'
    }).then((result) => {
        if (result.isConfirmed) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_post.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4) {
                        location.reload();
                    }
                };
                xhr.send("delete_post=true&post_id=" + postId);
            }
        });
    }
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
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                const currentUrl = window.location.href;
                const baseUrl = currentUrl.split('?')[0];
                history.replaceState({}, document.title, baseUrl);
            }
        });


    </script>
</body>
</html>
