<?php
session_start();

if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    $conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');
    $searchResults = array();
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT posts.*, users.username, users.profile_picture, GROUP_CONCAT(post_images.image_path) AS image_paths
            FROM users
            LEFT JOIN posts ON users.user_id = posts.user_id
            LEFT JOIN post_images ON posts.post_id = post_images.post_id
            WHERE users.user_id = ?
            GROUP BY posts.post_id
            ORDER BY posts.created_at DESC";


    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $posts = $result->fetch_all(MYSQLI_ASSOC);
        $searchResults['posts'] = $posts;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid user_id";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="style.css">
    <title>Stalker</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/5780/5780872.png">
    <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/5780/5780872.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
        .swiper-container {
            width: 300px;
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
        .char-count1 {
            position: absolute;
            right: 0;
            margin-right: 30px;
            top: 120px;
            color: #777;
            font-size: 11px;
        }
    .user-details {
        max-width: 600px;
        width: 100%;
        height: 190px;
        box-sizing: border-box;
        margin: 30px auto 0;
        background-color: #DFDFDF;
        border-radius: 30px;
        padding-left: 40px;
        padding-top: 30px;
    }
    .profile-picture1{
        width: 95px;
        height: 100px;
        max-height: 90px;
        border-radius: 50%;
        cursor: pointer;
    }
    .profile-picture1:hover{
        opacity: 80%;
    }
    .profile-picture2{
        width: 35px;
        height: 35px;
        border-radius: 50%;
        margin-right: 10px;
    }
    .user-header{
        margin-left: 10px;
        margin-top: 20px;
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
    .user-profile {
        position: relative;
    }

    .user-profile button {
        position: absolute;
        top: 0;
        right: 0;
        margin-top: 10px;
        margin-right: 80px;
        border: none;
        background: #333;
        color: white;
        padding: 8px;
        width: 200px;
        border-radius: 5px;
        cursor: pointer;
    }
    .user-profile button:first-child {
        top: 0;
    }

    .user-profile button:last-child{
        top: 60px; 
    }
    
    .user-profile button:hover{
        opacity: 80%;
    }
    .user-profile button i{
        left: 0;
        position: absolute;
        margin-left: 20px;
        font-size: 15px;
    }
    .end{
        text-align: center;
        font-size: 12px;
        margin-top:20px;
        color: #333;
    }
    .edit-prof{
        text-decoration: none;
        color: white;     
    }
    .msg-btn{
        text-decoration: none;
        color: white;
    }
    a.msg-btn{
        display: block;
        width: 100%;
        height: 100%;
        text-align: center;
        text-decoration: none;
        color: inherit;
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
    .overlay {
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    .popup {
        position: fixed;
        bottom: 0;
        right: 0;
        width: 320px;
        height: 350px;
        padding: 20px;
        background: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        z-index: 2;
        color: #333;
        border-radius: 30px;
        margin-right: 20px;
    }
    .close-btn{
        border: none;
        background: none;
        cursor: pointer;
        position: absolute;
        right: 0;
        top: 0;
        margin-right: 20px;
        margin-top: 20px;
        font-size: 18px;
    }
    .close-btn:hover{
        opacity: 80%;
    }
    .chat-name{
        font-size: 18px;
        text-align: left;
        margin-left: 50px;
    }
    .chat-pfp{
        width: 35px;
        position: absolute;
        left: 0;
        margin-left: 25px;
        border-radius: 50%;
        height: 35px;
    }
    .chat-area{
        position: absolute;
        bottom: 0;
        left: 0;
        height: 45px;
        width: 80%;
        margin-left: 15px;
        margin-bottom: 8px;
    }
    .chat-send{
        position: absolute;
        bottom: 0;
        right: 0;
        border: none;
        background: none;
        font-size: 30px;
        margin-right: 10px;
        margin-bottom: 8px;
        cursor: pointer;
    }
    .chat-send:hover{
        opacity: 80%;
    }
    
    
    .line1 {
        width: 100%;
        height: 1px;
        background-color: #afacac;
        margin-top: 17px;
        margin-bottom: 10px;
        }
    .chat-message.current-user {
        text-align: right;
    }

    .chat-message.other-user {
        text-align: left;
    }

    .chat-name-in {
        font-size: 15px;
        font-weight: bold;
    }

    .chat-content {
        font-size: 14px;
        margin-bottom: 5px;
        word-wrap: break-word; 
        white-space: normal;
    }
    .swiper-container {
            width: 100%;
        }
    .chat-messages {
        max-height: 200px; 
        overflow-y: auto; 
        padding: 10px; 
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
        }
        #CloseButton:hover{
            opacity: 80%;
        }
        .no-scroll {
            overflow: hidden;
        }
    @media (max-width: 920px) {
        body{
            width: 100%;
        }
        .user-profile button{
            width: 130px;
        }
        .user-profile i{
            display: none;
        }
        header {
            height: 60px;
        }
        #FullImage{
            margin-top: 10%;
        }
        header h1 {
            font-size: 18px;
        }
        
        .img-header{
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

        /* Additional styles for small screens */
        .textarea-container {
            margin-top: 10px;
        }

        .user-profile button{
            margin-right: 20px;
        }


        .post-container {
            padding: 10px; 
            font-size: 13px;
        }

        .profile-picture1 {
            width: 80px;
            height: 80px;
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
            width: 80%;
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
        .bi-trash-fill, .bi-caret-right-fill, .bi-x-circle{
            color: #333;
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
        <div class="overlay" id="overlay">
        <div class="popup" id="popup">
            <button class="close-btn" id="closeBtn" title="Close"><i class="bi bi-x-circle"></i></button>
            <img src="<?php echo $post['profile_picture']; ?>" alt="Profile Picture" class="chat-pfp"><h2 class="chat-name"><?php echo $post['username']; ?></h2>
            <div class="line1"></div>
            <div class="chat-section">
            <div class="chat-messages" id="chat-messages">
                
            </div>
            <div class="chat-input">
                <textarea class="chat-area" id="chatTextarea" placeholder="Type your message..."></textarea>
                <button onclick="sendMessage()" class="chat-send" title="Send"><i class="bi bi-caret-right-fill"></i></button>
        </div>
        </div>
        </div>
        </div>
        
    </header>
<main>
<?php if (isset($searchResults['posts']) && !empty($searchResults['posts'])): ?>
    <?php
        $isAuthor = ($post['user_id'] == $_SESSION['user_id']);
        $swiperContainerClass = 'swiper-container-' . $post['post_id'];
    ?>  
        <h2><a href="home.php"><i class="bi bi-arrow-left-circle"></i></a><?php echo $post['username']; ?>'s Profile</h2>
    <div class="user-details">
        <div class="user-profile">
            <img id="profilePicture" src="<?php echo $post['profile_picture']; ?>" alt="Profile Picture" class="profile-picture1" title="View Photo" onclick="FullView(this.src)"><br>
            <h2 id="username">
                <a class="username">
                    <strong><?php echo $post['username']; ?></strong>
                </a>
            </h2>
            <?php if (!$isAuthor): ?>
            <button id="addToContactsBtn"><i class="bi bi-envelope-check"></i> Add to contacts</button>
            <button class="msg-btn" id="messageBtn"><i class="bi bi-chat-square-heart"></i>Message</button>
            <?php endif; ?>
            <?php if ($isAuthor): ?>
            <p><button class="edit-prof-btn" title="Go to profile"><a href="profile.php" class="edit-prof"><i class="bi bi-pencil-square"></i>Edit Profile</a></button></p>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
<?php endif; ?>


<?php 
if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    $conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');
    $searchResults = array();
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT posts.*, users.username, users.profile_picture, GROUP_CONCAT(post_images.image_path) AS image_paths
            FROM posts
            INNER JOIN users ON posts.user_id = users.user_id
            LEFT JOIN post_images ON posts.post_id = post_images.post_id
            WHERE posts.user_id = ?
            GROUP BY posts.post_id
            ORDER BY posts.created_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $posts = $result->fetch_all(MYSQLI_ASSOC);
        $searchResults['posts'] = $posts;
    }

    $stmt->close();
    $conn->close();
}
if (isset($searchResults['posts']) && !empty($searchResults['posts'])): ?>
    <h3 class="user-header"><?php echo $post['username']; ?>'s thoughts:</h3>
    <?php foreach ($searchResults['posts'] as $post): ?>
        <?php
        $isAuthor = ($post['user_id'] == $_SESSION['user_id']);
        $swiperContainerClass = 'swiper-container-' . $post['post_id'];
        ?>

        <div class="post-container" data-post-id="<?php echo $post['post_id']; ?>">
            <div class="user-info">
                <img src="<?php echo $post['profile_picture']; ?>" alt="Profile Picture" class="profile-picture2">
                <p class="username"><strong><?php echo $post['username']; ?></strong></p>
                <p class="timestamp"><?php echo $post['created_at']; ?></p>
            </div>
            <div class="line"></div>
            <div class="edit-dlt" style="position: absolute; top: 0; right: 0; margin-top: 20px; margin-right: 15px;">
                <?php if ($isAuthor): ?>
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

<div class="end">
    </div>
</main>
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

document.getElementById('messageBtn').addEventListener('click', function () {
    document.getElementById('overlay').style.display = 'block';
    loadMessages(); 
});

document.getElementById('closeBtn').addEventListener('click', function () {
    document.getElementById('overlay').style.display = 'none';
});

document.getElementById('sendMsgBtn').addEventListener('click', function () {
    sendMessage();
});

function loadMessages() {
    const receiverId = <?php echo $userId; ?>;
    const currentUserId = <?php echo $_SESSION['user_id']; ?>;
    const messagesContainer = document.querySelector('.chat-messages');
    messagesContainer.innerHTML = '';
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    fetch('load_messages.php?receiver_id=' + receiverId, {
        method: 'GET',
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const messagesContainer = document.querySelector('.chat-messages');

            if (data.messages.length === 0) {
                const noMessagesDiv = document.createElement('div');
                noMessagesDiv.className = 'chat-message';
                noMessagesDiv.innerHTML = `
                    <p class="chat-content">No messages yet, say hi to ${data.username} to begin with!</p>
                `;
                messagesContainer.appendChild(noMessagesDiv);
            } else {
                data.messages.forEach(message => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'chat-message';

                    if (message.sender_id == currentUserId) {
                        messageDiv.classList.add('current-user');
                    } else {
                        messageDiv.classList.add('other-user');
                    }

                    messageDiv.innerHTML = `
                        <p class="chat-name-in">${message.username}:</p>
                        <p class="chat-content">${message.message_content}</p>
                    `;

                    messagesContainer.appendChild(messageDiv);
                });
            }

            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        } else {
            // Handle error if needed
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}


function sendMessage() {
    const messageContent = document.getElementById('chatTextarea').value;
    const receiverId = <?php echo $userId; ?>;

    fetch('send_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `message=${encodeURIComponent(messageContent)}&receiver_id=${receiverId}`,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('chatTextarea').value = '';
            loadMessages();
        } else {
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
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

<script>

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


</script>
<script>
document.getElementById('addToContactsBtn').addEventListener('click', function () {
    addToContacts();
});

function addToContacts() {
    var userId = <?php echo $userId; ?>;

    fetch('add_contact_backend.php?user_id=' + userId, {
        method: 'GET',
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Added to Contacts',
                text: 'The user has been added to your contacts.',
                showConfirmButton: false,
                timer: 1000
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'The user is already in your contacts.',
                showConfirmButton: false,
                timer: 1500
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'The user is already in your contacts.',
            showConfirmButton: false,
            timer: 1500
        });
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
</script>
</body>
</html>
