<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchResults = array();
$usersWithoutPosts = array(); 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $searchTerm = $_POST['search'];

    $hasPostsQuery = "SELECT 1 FROM posts WHERE user_id = (SELECT user_id FROM users WHERE username = ?) LIMIT 1";
    $stmtHasPosts = $conn->prepare($hasPostsQuery);
    $stmtHasPosts->bind_param("s", $searchTerm);
    $stmtHasPosts->execute();
    $stmtHasPosts->store_result();

    if ($stmtHasPosts->num_rows > 0) {
        $query = "SELECT users.*, posts.*, GROUP_CONCAT(post_images.image_path) AS image_paths
            FROM users
            LEFT JOIN posts ON users.user_id = posts.user_id
            LEFT JOIN post_images ON posts.post_id = post_images.post_id
            WHERE users.username = ?
            GROUP BY users.user_id
            ORDER BY posts.created_at DESC";
    } else {
        $query = "SELECT users.*, NULL AS post_id, NULL AS post_content, NULL AS created_at, NULL AS image_paths
            FROM users
            WHERE NOT EXISTS (
                SELECT 1 FROM posts WHERE users.user_id = posts.user_id
            )
            AND users.username = ?
            ORDER BY users.username";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $searchTerm);

    $stmt->execute();

    if ($stmt->errno) {
        die('Error executing statement: ' . $stmt->error);
    }

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if ($row['post_id'] !== null) {
            $searchResults['posts'][] = $row;
        } else {
            $userWithoutPosts = [
                'user_id' => $row['user_id'],
                'username' => $row['username'],
                'profile_picture' => $row['profile_picture'],
            ];

            $searchResults['usersWithoutPosts'][] = $userWithoutPosts;
        }
    }

    $stmt->close();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="style.css">
    <title>Search Results</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>


    <style>
        
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
        height: 260px;
        box-sizing: border-box;
        margin: 10px auto 0;
        background-color: #DFDFDF;
        border-radius: 30px;
        padding: 10px;
    }
    .profile-picture1{
        width: 108px;
        height: 105px;
        max-height: 120px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto;
    }
    .profile-picture2{
        width: 35px;
        height: 35px;
        border-radius: 50%;
        margin-right: 10px;
    }
    .user-header{
        margin-top: 20px;
        margin-left: 10px;
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
    .user-profile h2{
        text-align: center;
    }
    .user-profile {
    }

    .user-profile button {
        border: none;
        background: #333;
        color: white;
        padding: 8px;
        width: 200px;
        border-radius: 5px;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px auto; 
    }

    .user-profile button:last-child {
        top: 40px; 
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
    .user-profile a{
        text-decoration: none;
        color: white;
    }
    .end{
        text-align: center;
        font-size: 12px;
        margin-top: 20px;
        color: #333;
    }
    .edit-prof{
        text-decoration: none;
        color: white;
    }
    .bi-eye{
        font-size: 15px;
    }
    a.view-btn {
        display: block;
        width: 100%;
        height: 100%;
        text-align: center;
        text-decoration: none;
        color: inherit;
    }  
    a.message-btn{
        display: block;
        width: 100%;
        height: 100%;
        text-align: center;
        text-decoration: none;
        color: inherit;
    }
    a.edit-prof{
        display: block;
        width: 100%;
        height: 100%;
        text-align: center;
        text-decoration: none;
        color: inherit;
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
    
    </style>
</head>

<body>
<?php
if (empty($searchResults['posts']) && empty($searchResults['usersWithoutPosts'])) {
    echo "<div class='no_result'>
    <i class='bi bi-emoji-frown'></i>
        <p>No results found</p>
    </div>";
} else {
    if (isset($searchResults['posts']) && !empty($searchResults['posts'])) {
        foreach ($searchResults['posts'] as $post) {
            $isAuthor = ($post['user_id'] == $_SESSION['user_id']);
            $swiperContainerClass = 'swiper-container-' . $post['post_id'];
            $messageBtnId = 'messageBtn_' . $post['user_id'];   
            ?>
            <div class="user-details">
                <div class="user-profile">
                    <img id="profilePicture" src="<?php echo $post['profile_picture']; ?>" alt="Profile Picture" class="profile-picture1"><br>
                    <h2 id="username"><?php echo $post['username']; ?></h2>
                    <?php if (!$isAuthor): ?>
                        <button><a class="view-btn" href="stalk.php?user_id=<?php echo $post['user_id']; ?>"> View Profile</a></button>
                    <?php endif; ?>
                    <?php if ($isAuthor): ?>
                        <p><button class="edit-prof-btn"><a href="stalk.php?user_id=<?php echo $post['user_id']; ?>" class="edit-prof" title="Go to profile">View Profile</a></button></p>
                    <?php endif; ?>
                </div>
            </div>
          
            <?php
        }
    }

    if (isset($searchResults['usersWithoutPosts']) && !empty($searchResults['usersWithoutPosts'])) {
        foreach ($searchResults['usersWithoutPosts'] as $userWithoutPosts) {
            $isAuthor = ($userWithoutPosts['user_id'] == $_SESSION['user_id']);
            ?>
            <div class="user-details">
                <div class="user-profile">
                    <img id="profilePicture" src="<?php echo $userWithoutPosts['profile_picture']; ?>" alt="Profile Picture" class="profile-picture1"><br>
                    <h2 id="username"><?php echo $userWithoutPosts['username']; ?></h2>
                    <?php if (!$isAuthor): ?>
                        <button><a class="view-btn" href="stalk.php?user_id=<?php echo $userWithoutPosts['user_id']; ?>">View Profile</a></button>
                        
                    <?php endif; ?>
                    <?php if ($isAuthor): ?>
                        <p><button class="edit-prof-btn"><a href="stalk.php?user_id=<?php echo $userWithoutPosts['user_id']; ?>" class="edit-prof" title="Go to profile">View Profile</a></button></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
    }
    echo "<div class='end'>
            <p>-- Nothing to see here. --</p>
        </div>";
}

?>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
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
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('addToContactsBtn').addEventListener('click', function () {
        addToContacts();
    });
    function addToContacts() {
        fetch('add_contact_backend.php', {
            method: 'POST',
        })
        .then(response => response.json())
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
        });
    }
});
</script>

</body>
</html>
