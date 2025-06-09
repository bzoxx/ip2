<?php require_once "checklogin.php"; 

// Get the receiver ID from URL
$receiver_id = isset($_GET['user']) ? (int)$_GET['user'] : 0;
if (!$receiver_id) {
    header("Location: chatlist.php");
    exit;
}

// Get receiver details and check for approved match
try {
    $connect->exec("USE dating_app");
    
    // First get receiver details
    $stmt = $connect->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$receiver_id]);
    $receiver = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$receiver) {
        header("Location: chatlist.php");
        exit;
    }

    // Check for approved match
    $stmt = $connect->prepare("SELECT * FROM matches WHERE 
        ((connect = ? AND wtih = ?) OR (connect = ? AND wtih = ?)) 
        AND approved = 1");
    $stmt->execute([
        $_SESSION['user_id'], 
        $receiver_id,
        $receiver_id,
        $_SESSION['user_id']
    ]);
    
    if ($stmt->rowCount() === 0) {
        // No approved match found
        header("Location: chatlist.php?error=no_match");
        exit;
    }

} catch(PDOException $e) {
    error_log("Error: " . $e->getMessage());
    header("Location: chatlist.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?= htmlspecialchars($receiver['username']) ?> - Blind Date Hub</title>
    <link rel="stylesheet" href="css/chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body>
    <!-- Back Button -->
    <div class="back-button">
        <a href="chatlist.php"><i class="fas fa-arrow-left"></i> Back to Chat List</a>
    </div>

    <!-- Main Content -->
    <main>
        <h1>Chat with <?= htmlspecialchars($receiver['username']) ?></h1>
        <div class="chat-box">
            <div class="messages" id="messages">
                <!-- Chat messages will be loaded here -->
            </div>
            <div style="display: flex;">
                <input type="text" id="messageInput" placeholder="Type a message...">
                <button id="sendMessage">Send</button>
            </div>
        </div>
    </main>


    <script>
    const messagesDiv = document.getElementById('messages');
    const messageInput = document.getElementById('messageInput');
    const sendMessageButton = document.getElementById('sendMessage');
    const receiverId = <?= $receiver_id ?>;
    let lastMessageId = 0;

    // Function to load messages
    function loadMessages() {
        fetch(`get_messages.php?receiver_id=${receiverId}&last_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        addMessage(msg.message, msg.sender_id == <?= $_SESSION['user_id'] ?> ? 'user' : 'receiver');
                        lastMessageId = Math.max(lastMessageId, msg.id);
                    });
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }
            })
            .catch(error => console.error('Error loading messages:', error));
    }

    // Function to send message
    function handleSendMessage() {
        const message = messageInput.value.trim();
        if (message) {
            const formData = new FormData();
            formData.append('receiver_id', receiverId);
            formData.append('message', message);

            fetch('send_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addMessage(message, 'user');
                    messageInput.value = '';
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                    lastMessageId = data.message_id;
                }
            })
            .catch(error => console.error('Error sending message:', error));
        }
    }

    // Function to add message to chat
    function addMessage(text, type) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', type);
        messageDiv.textContent = text;
        messagesDiv.appendChild(messageDiv);
    }

    // Event listeners
    sendMessageButton.addEventListener('click', handleSendMessage);
    messageInput.addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            handleSendMessage();
            event.preventDefault();
        }
    });

    // Load initial messages and set up polling
    loadMessages();
    setInterval(loadMessages, 3000); // Poll for new messages every 3 seconds
    </script>
</body>
</html>
