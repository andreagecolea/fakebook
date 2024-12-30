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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
    $query = "SELECT posts.*, users.username, users.profile_picture, GROUP_CONCAT(post_images.image_path) AS image_paths
            FROM posts
            INNER JOIN users ON posts.user_id = users.user_id
            LEFT JOIN post_images ON posts.post_id = post_images.post_id
            WHERE users.username = ?
            GROUP BY posts.post_id
            ORDER BY posts.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $searchTerm);

    $stmt->execute();

    if ($stmt->errno) {
        die('Error executing statement: ' . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $posts = $result->fetch_all(MYSQLI_ASSOC);
        $searchResults['posts'] = $posts;
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Search Results</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
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
        margin: 10px auto 0;
        background-color: #DFDFDF;
        border-radius: 30px;
        padding-left: 40px;
        padding-top: 30px;
    }
    .profile-picture1{
        width: 100px;
        height: 100px;
        max-height: 90px;
        border-radius: 50%;
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
        margin-left: 10px;
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

    .user-profile button:last-child {
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
    </style>
</head>
<body>
<?php
if (isset($searchResults['posts']) && !empty($searchResults['posts'])):
        $isAuthor = ($post['user_id'] == $_SESSION['user_id']);
        $swiperContainerClass = 'swiper-container-' . $post['post_id'];
        ?>
<div class="user-details">
        <div class="user-profile">
            <img id="profilePicture" src="<?php echo $post['profile_picture']; ?>" alt="Profile Picture" class="profile-picture1"><br>
            <h2 id="username"><?php echo $post['username']; ?></h2>
            <?php if (!$isAuthor): ?>
            <button id="addToContactsBtn"><i class="bi bi-envelope-check"></i> Add to contacts</button>
            <button> <a href="messages.php?user_id=<?php echo $post['user_id']; ?>"><i class="bi bi-chat-square-heart"></i>Message</a></button>
            <?php endif; ?>
            <?php if ($isAuthor): ?>
            <p><button class="edit-prof-btn"><a href="profile.php" class="edit-prof" title="Go to profile"><i class="bi bi-pencil-square"></i>Edit Profile</a></button></p>
            <?php endif; ?>
        </div>
    </div>
    <h3 class="user-header"><?php echo $post['username']; ?>'s thoughts:</h3>
<?php else: ?>
<?php endif; ?>
<?php
if (isset($searchResults['posts']) && !empty($searchResults['posts'])):
    foreach ($searchResults['posts'] as $post):
        $isAuthor = ($post['user_id'] == $_SESSION['user_id']);
        $swiperContainerClass = 'swiper-container-' . $post['post_id'];
        ?>

<div class="post-container" data-post-id="<?php echo $post['post_id']; ?>">
                <div class="user-info">
                <img
                src=<?php echo $post['profile_picture']; ?>
                alt="Profile Picture" class="profile-picture2">
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
            <p class="content" id="postContent_<?php echo $post['post_id']; ?>">
            <?php echo $post['content']; ?>
            </p>
            
    
            <?php if (!empty($post['image_paths'])): ?>
                <div class="swiper-container swiper-container-<?php echo $post['post_id']; ?>">
                    <div class="swiper-wrapper">
                        <?php foreach (explode(',', $post['image_paths']) as $imagePath): ?>
                            <div class="swiper-slide">
                                <img src="<?php echo $imagePath; ?>" alt="Post Image" class="swiper-image">
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
    <p>No results found.</p>
<?php endif; ?>
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
