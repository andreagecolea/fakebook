    <?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'User not logged in']);
        exit();
    }

    $conn = new mysqli('sql305.infinityfree.com', 'if0_35561106', '3qFm4JJOBXeExQ', 'if0_35641815_webapp_db');
    if ($conn->connect_error) {
        echo json_encode(['error' => 'Database connection failed']);
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $query = "SELECT contacts.*, users.username
            FROM contacts
            INNER JOIN users ON contacts.contact_id = users.user_id
            WHERE contacts.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $contacts = [];
    while ($row = $result->fetch_assoc()) {
        $contacts[] = $row['contact_id'];
    }

    $stmt->close();

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Messages</title>
        <link rel="icon" href="https://cdn0.iconfinder.com/data/icons/apple-apps/100/Apple_Messages-512.png">
        <link rel="shortcut icon" href="https://cdn0.iconfinder.com/data/icons/apple-apps/100/Apple_Messages-512.png">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
        <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
        <style>
            body {
                font-family: 'Poppins', sans-serif;
                margin: 0;
                padding: 0;
                display: flex;
            }


            #contacts {
                width: 20%;
                background-color: #f1f1f1;
                padding: 20px;
                height: 96vh;
                overflow: auto;
            }

            #contacts i{
                margin-right: 10px;
                color: #333;
                text-align: left;
            }
            #contacts i:hover{
                opacity: 80%;
            }
            #messages {
                flex: 1;
                padding: 20px;
            }
            .text-area{
                resize: none;
                width: 130vh;
                bottom: 0;
                position: fixed;   
                height: 25px;
                margin-bottom: 10px;
                border-radius: 30px;
                padding-left: 10px;
                padding-top: 8px;
                right: 0;
                margin-right: 180px;
            }
            .send-btn{
                position: fixed; 
                bottom: 0;
                right: 0;
                margin-right: 100px;
                border: none;
                background-color: #333;
                border-radius: 30px;
                padding: 10px;
                width: 70px;
                margin-bottom: 10px;
                color: white;
                cursor: pointer;
            }
            .send-btn:hover{
                opacity: 80%;
            }
            .bi-images{
                position: fixed;  
                bottom: 0;
                font-size: 24px;
                margin-left: 10px;
                margin-bottom: 10px;
                cursor: pointer;
            }
            .bi-images:hover{
                opacity: 80%;
            }
            .line {
                width: 100%;
                height: 1px;
                background-color: #afacac;
                margin: 10px 0;
            }
            .line1{
                width: 100%;
                height: 2px;
                background-color: #afacac;
                margin: 10px 0;
            }
            .label-add{
                font-size: 18px;
            }
            .search{
                padding: 7px;
                width: 58%;
                border-radius: 30px;
                padding-left: 7px;
                margin-left: 44px;
                margin-bottom: 10px;
                margin-top: 8px;
            }
            .search-btn{
                border: none;
                background: none;
            }
            .bi-search{
                font-size: 16px;
                cursor: pointer;
            }
            .bi-arrow-left-circle{
                font-size: 28px;
                margin-top: 5px;
            }   
            #chat-popup {
                position: fixed;
                width: 950px;
                height: 380px;
                padding: 20px;
                background: #fff;
                z-index: 2;
                color: #333;
                top: 50%;
                left: 50%;
                transform: translate(-35%, -48%);
                overflow-y: auto; 
            }
            .message-left {
                text-align: left;
                word-wrap: break-word;
            }

            .message-right {
                text-align: right;
                word-wrap: break-word;
            }
            .close-chat{
                border: 1px solid black;
                background-color: white;
                padding-left: 50px;
                padding-right: 50px;
                padding-top: 7px;
                padding-bottom: 7px;
                border-radius: 30px;
                cursor: pointer;
                bottom: 0;
                left: 50%;
                margin-bottom: 55px;
                position: fixed ;
            }
            .select-msg{
                text-align: center;
                margin-top: 200px;
            }
            .select-msg i{
                font-size: 50px;
            }
            .select-msg p{
                font-size: 17px;
                font-weight: bold;
            }
            .message-container {
                margin-bottom: 5px;
                white-space: normal;
                overflow-y: auto; 
            }
            .back-home{
                margin-left: 50px;
                font-size: 18px;    
            }
            .no-contact{
                text-align: center;
                margin-top: 120px;
            }
            .no-contact i{
                font-size: 38px;
                opacity: 90%;
            }
            .no-contact p{
                font-size: 12px;
            }
            ul {
                position: absolute;
                left: 0;
                width: 270px;
                padding: 0; 
            }

            li {
                list-style-type: none;
                text-decoration: none;
                margin-bottom: 20px;
            }

            li a {
                text-decoration: none;
                color: black;
                background-color: #E3E1E1;
                padding: 10px 75px;
                border-radius: 30px;
                display: block;
                text-align: center;
            }

            li a:hover {
                opacity: 80%;
            }
            .container {
                position: relative; 
            }
            #message-container {
                flex: 1;
            }
            .show{
                display: none;
            }
            @media only screen and (max-width: 1300px){
                #contacts{
                    width: 35%;
                }
                #chat-popup{
                    width: 600px;
                    height: 100%;
                    border-radius: 30px;
                    transform: translate(-15%, -50%);
                }
                .close-chat{
                  left: 65%;
                }
                .text-area{
                    width: 60vh;
                    margin-right: 85px;
                }
                .send-btn{
                    margin-right: 10px;
                }
                .bi-x-lg{
                    color: #333;
                }
            }
            
            @media only screen and (max-width: 1185px) {
                .text-area{
                    width: 50vh;
                    margin-right: 85px;
                }
            }
            @media only screen and (max-width: 1125px) {
                .text-area{
                    width: 40vh;
                    margin-right: 85px;
                }
            }
            @media only screen and (max-width: 1080px){
                #chat-popup{
                    width: 500px;
                }

            }
            @media only screen and (max-width: 990px){
                #chat-popup{
                    width: 400px;
                }

            }
            @media only screen and (max-width: 870px){
                .text-area{
                    width: 35vh;
                }

            }
            @media only screen and (max-width: 778px) {

                #contacts{
                    width: 100%;
                }
                #message-container {
                    width: 100%;
                }
                #messages{
                    display: none;
                }
                #chat-popup{
                    width: 300px;
                    height: 100%;
                    border-radius: 30px;
                    transform: translate(-50%, -52%);
                }
                .close-chat{
                  left: 35%;
                }
                .text-area{
                    width: 40vh;
                    margin-right: 85px;
                }
                .send-btn{
                    margin-right: 10px;
                }
            }
            @media only screen and (max-width: 450px) {
                #chat-popup{
                    width: 330px;
                    height: 70%;
                    border-radius: 30px;
                    transform: translate(-50%, -56%);
                }
            .text-area{
                width: 37vh;
            }
            ul{
                width: 340px;
            }
            }
        </style>
    </head>
    <body>
        <header>
        </header>
        <button id="toggle-contacts-btn" class="show" title="Show chats"><i class="bi bi-list"></i></button>
        <div id="contacts">
        <div id="contacts-container">
            <p class="back-home"><a href="home.php" title="Back to Home"><i class="bi bi-arrow-left-circle"></i></a>Home</p>
            <div class="line"></div>
            <h2 class="label-add">Saved contacts:</h2>
            <?php
                if (empty($contacts)) {
                    echo "<div class='no-contact'><i class='bi bi-emoji-frown'></i><p>No added contacts yet.<br>Go to the homepage to add some contacts!</p></div>";
                } else {
                    echo "<div class='container'><ul>";
                    foreach ($contacts as $contact_id) {
                        $query = "SELECT username FROM users WHERE user_id = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("i", $contact_id);
                        $stmt->execute();
                        $result_username = $stmt->get_result();
                    
                        if ($result_username->num_rows > 0) {
                            $row_username = $result_username->fetch_assoc();
                            $username = $row_username['username'];
                            echo "<li><a id='contact-$contact_id' class='contact-link' data-contact-id='$contact_id' data-username='$username' href=''>" . htmlspecialchars($username) . "</a></li>";
                        }
                    
                        $stmt->close();
                    }
                    echo "</ul></div>";
                }
                ?>
        </ul>
        </div>
        </div>
        <div id="chat-popup" style="display: none;">
            <div id="message-container" class="message-container"></div>
        </div>
        <div id="chat-popup1" style="display: none;">
        <button id="close-popup" class="close-chat" title="Close Chat"><i class="bi bi-x-lg"></i></button>
            <div class="message-input">
            <input type="hidden" id="contact-id" name="contact_id" value="" required>
                <textarea id="message-textarea" placeholder="Type your message..." class="text-area" required oninvalid="this.setCustomValidity('Oops! Message cannot be empty')" oninput="setCustomValidity('')"></textarea>
                <button type="submit" id="send-btn" class="send-btn">Send</button>
            </div>
        </div>  
        <div id="messages">
        <h2 id="selected-username">Chat with: </h2>
            <div class="line1"></div>
            <div id="message-container">
            <form id="message-form">
                <input type="hidden" id="contact-id" name="contact_id" value="">
                <div id="message-container">
                </div>
                <div class="select-msg">
                <i class="bi bi-chat-quote"></i>
                <p>Select a contact to chat!</p>
                </div>
            </form>
            </div>
        </div>
        <script>
       $(document).ready(function () {
    $('.contact-link').on('click', function (e) {
        e.preventDefault();

        contactId = $(this).data('contact-id');
        var username = $(this).data('username');

        $('#chat-popup, #chat-popup1').hide();
        $('#selected-username').text('Chat with: ' + username);
        loadMessages(contactId);
    });

    function loadMessages(contactId) {
        $.ajax({
            type: 'POST',
            url: 'get_messages.php',
            data: { contact_id: contactId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    displayChatPopups(contactId, response.messages);
                } else {
                    alert('Failed to fetch messages. ' + response.error);
                }
            },
            error: function (xhr, status, error) {
                alert('Error fetching messages. ' + error);
            }
        });
    }

    function displayChatPopups(contactId, messages) {
    $('#contact-id').val(contactId);

    var messagesContainer = $('#chat-popup #message-container');
    messagesContainer.empty();

    if (messages.length > 0) {
        for (var i = 0; i < messages.length; i++) {
            var message = messages[i];
            var messageText = message.message_content;
            var senderId = message.sender_id;
            var username = message.username;

            var messageClass = (senderId == contactId) ? 'received-message chat-content' : 'sent-message chat-content';
            var messageDirectionClass = (senderId == contactId) ? 'message-left chat-content' : 'message-right chat-content';

            messagesContainer.append('<div style="font-weight:bold;" class="' + messageClass + ' ' + messageDirectionClass + '">' + username + ':</div>');
            messagesContainer.append('<div style="margin-bottom:10px;" class="' + messageClass + ' ' + messageDirectionClass + '">' + messageText + '</div>');
        }
        messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
    } else {
        var username = $('#contact-' + contactId).text();
        var noMessagesText = "<i class='bi bi-chat-square-heart'></i><br>No messages yet, say hi to " + username + " to begin with!";
        messagesContainer.append('<div class="select-msg">' + noMessagesText + '</div>');
    }

    $('#chat-popup, #chat-popup1, #selected-username').show();
}


    $('#close-popup').on('click', function () {
        $('#chat-popup, #chat-popup1, #selected-username').hide();
    });

    document.getElementById('send-btn').addEventListener('click', function () {
        sendMessage(contactId);
    });
    function sendMessage(contactId) {
        const messageContent = document.getElementById('message-textarea').value;
        const receiverId = contactId;

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
                document.getElementById('message-textarea').value = '';
                loadMessages(contactId);
            } else {
                alert('An error occurred. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
});


        


    </script>
    </body>
    </html>
    <?php
    $conn->close();
    ?>