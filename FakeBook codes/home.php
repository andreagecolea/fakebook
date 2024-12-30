<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($username);

    if ($stmt->fetch()) {
    } else {
        $username = 'Guest';
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: index.html");
    exit();
}

$conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        $postContent = trim($_POST['postContent']);
        $imagePaths = array();

        foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
            $imageFile = $_FILES['image']['name'][$key];
            
            if (!empty($imageFile)) {
                $targetDir = "uploads/";
                $targetFile = $targetDir . basename($imageFile);
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                if (move_uploaded_file($tmp_name, $targetFile)) {
                    $imagePaths[] = $targetFile;
                } else {
                    echo "Error: Sorry, there was an error uploading your file.";
                    exit();
                }
            }
        }

        $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $postContent, $imagePaths);
        $stmt->execute();
        $stmt->close();
        $postId = $conn->insert_id;

        foreach ($imagePaths as $imagePath) {
            $stmtImage = $conn->prepare("INSERT INTO post_images (post_id, image_path) VALUES (?, ?)");
            $stmtImage->bind_param("is", $postId, $imagePath);
            $stmtImage->execute();
            $stmtImage->close();
        }

            }
        }
        $query = "SELECT posts.*, users.username, users.profile_picture, GROUP_CONCAT(post_images.image_path) AS image_paths
                FROM posts 
                INNER JOIN users ON posts.user_id = users.user_id
                LEFT JOIN post_images ON posts.post_id = post_images.post_id
                GROUP BY posts.post_id
                ORDER BY posts.created_at DESC";




$result = $conn->query($query);

if ($result->num_rows > 0) {
    $posts = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $posts = array();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thoughts</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <link rel="icon" href="https://clipart-library.com/images_k/thought-bubble-png-transparent/thought-bubble-png-transparent-14.png">
    <link rel="shortcut icon" href="https://clipart-library.com/images_k/thought-bubble-png-transparent/thought-bubble-png-transparent-14.png">
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
            padding: 20px;
            box-sizing: border-box;
            margin: 80px auto 0;
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
        .char-count1 {
            position: absolute;
            right: 0;
            margin-right: 30px;
            top: 140px;
            color: #777;
            font-size: 11px;
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
        .user-info {
            display: flex;
            align-items: center;
        }

        .profile-picture {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .username {
            font-weight: bold;
            margin-bottom: 8px;
            text-decoration: none;
            color: black;
        }
        .username:hover{
            opacity: 80%;
            text-decoration: underline;
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
            margin-top: 90px;
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
            font-size: 17px;
            color: white;
        }
        .swiper-container {
            width: 100%; /* Set width to 100% for responsiveness */
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
            font-size: 20px;
            position: absolute;
            color: black;
        }
        .end{
            text-align: center;
            font-size: 12px;
            margin-bottom: 20px;
            color: #333;
        }
        footer{
            margin-top: 30px;
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
            width: 100%;
            bottom: 0; 
            height: 60px;
            z-index: 1000;
        }
        .profile-picture a{
            text-decoration: none;
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
        #CloseButton{
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
        header {
            height: 60px;
        }

        #FullImage{
            margin-top: 10%;
        }
        .img-header {
            display: none;
        }

        main {
            
            width: 100%;
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


        /* Additional styles for small screens */
        .textarea-container {
            margin-top: 10px;
        }


        .file-label{
            margin-top: 60px;
        }
        .post-container {
            padding: 10px; /* Add some padding to the post container */
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
            width: 80%;
        }

        .swiper-pagination {
            bottom: 5px;
        }

        .msg-btn i,
        .profile-btn i,
        .bi-search {
            font-size: 20px;
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
        .content{
            font-size: 13px;
        }
        .bi-trash-fill{
            color: #333;
        }
    }
    </style>
    <title>Home</title>
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
        <form action="search.php" method="post" id="searchForm">
            <input type="text" name="search" placeholder="Search user" required oninvalid="this.setCustomValidity('Username cannot be empty')" oninput="setCustomValidity('')">
            <button type="submit" name="submit"><i class="bi bi-search"></i></button>
        </form>
        </div>

    </header>
    <main>
        <form action="post.php" method="post" enctype="multipart/form-data">
            <div class="textarea-container">
                <textarea id="post-area" class="post-area" name="postContent" placeholder="Any random thoughts, <?php echo $username; ?>? " maxlength="255" oninput="updateCharCount(this)"></textarea>
                <div class="char-count">0/255</div>
            </div>
            <label for="image" class="file-label">
            <i class="bi bi-camera-fill"></i> Upload Image
            <span id="selectedFileName"></span>
                </label>
                <input type="file" name="image[]" id="image" accept=".jpg, .jpeg, .png, .gif, .webp" style="display: none;" multiple>
            <button type="submit" name="submit" id="submit" class="button">Post</button>
        </form>
        <?php foreach ($posts as $post): ?>
            <?php
                $isAuthor = ($post['user_id'] == $_SESSION['user_id']);
                $swiperContainerClass = 'swiper-container-' . $post['post_id'];
            ?>
            <div class="post-container" data-post-id="<?php echo $post['post_id']; ?>">
                <div class="user-info">
                <img src="<?php echo $post['profile_picture']; ?>" alt="Profile Picture" class="profile-picture">
                <a class="username" href="stalk.php?user_id=<?php echo $post['user_id']; ?>"><strong><?php echo $post['username']; ?></strong></a>
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
            <p class="content" id="postContent_<?php echo $post['post_id']; ?>">
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
    <div id="FullImageView">
        <img id="FullImage"/>
        <span id="CloseButton" onclick="CloseFullView()" title="Close"><i class="bi bi-x-circle"></i></span>
        </div>
    </main>
    <div class="end">
        <p>-- Nothing to see here. --</p>
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
        const imageInput = document.getElementById('image');
        const selectedFileName = document.getElementById('selectedFileName');

        imageInput.addEventListener('change', function () {
            const files = imageInput.files;
            if (files.length > 0) {
                selectedFileName.textContent = files[0].name;
            } else {
                selectedFileName.textContent = '';
            }
        });

        const submitButton = document.getElementById('submit');

        submitButton.addEventListener('click', function (event) {
            const postContent = document.getElementById('post-area').value;
            const errorMessage = document.getElementById('error-message');

            if (postContent.trim() === '' && imageInput.files.length === 0) {
                event.preventDefault();
                errorMessage.textContent = 'Post content or image cannot be empty.';
                return;
            }

            errorMessage.textContent = '';
        });
    });

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

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const loginStatus = urlParams.get('success');

            if (loginStatus === '1') {
                Swal.fire({
                    icon: 'success',
                    title: 'Login Successfully!',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });

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

        function updateCharCount(textarea) {
            const maxLength = textarea.getAttribute('maxlength');
            const currentLength = textarea.value.length;
            const charCountElement = textarea.nextElementSibling;
            charCountElement.textContent = `${currentLength}/${maxLength}`;
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
