<?php require_once "checklogin.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Blind Date Hub</title>
    <link rel="stylesheet" href="css/chat.css">
    <link rel="icon" type="image/png" href="images/logo.png">


</head>
<body>

    <!-- Header -->
    <header>
        <div class="logo">
            <h1>Blind Date Hub</h1>
        </div>
        <nav>
            <ul>
                <li><a href="dashboard.php" aria-label="Go to Dashboard">Dashboard</a></li>
                <li><a href="matchmaking.php" aria-label="Start Matchmaking">Matchmaking</a></li>
                <li><a href="about.php" aria-label="Learn How Blind Date Hub Works">How It Works</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <h1>Chat with Match</h1>
        <section class="tags">
            <div class="tag">Horror Movies</div>
            <div class="tag">Skateboarding</div>
            <div class="tag">Reading</div>
            <div class="tag">Cooking</div>
            <div class="tag">Traveling</div>
            <div class="tag">Photography</div>
        </section>
        <div class="chat-box">
            <div class="messages" id="messages">
                <!-- Chat messages will go here -->
            </div>
            <div style="display: flex;">
                <input type="text" id="messageInput" placeholder="Type a message...">
                <button id="sendMessage">Send</button>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Blind Date Hub. All rights reserved.</p>
        <p><a href="about.php">About</a> | <a href="privacyPolicy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
    </footer>

   <script>
    
    const messagesDiv = document.getElementById('messages');
const messageInput = document.getElementById('messageInput');
const sendMessageButton = document.getElementById('sendMessage');

// Default responses for common messages
const defaultResponses = {
    "hi": "Hi!",
    "hey": "hey there!",
    "hello": "Hello!",
    "how are you": "good! How are you doing?",
    "what's up": "Not much, what about you?",
    "im doing well": "nice",
    "bye": "Goodbye! Have a great day!",
    "thank you": "You're welcome!",
    "who are you": "I am your friendly chat bot!"
};

// Function to handle sending a message
function handleSendMessage() {
    const userMessage = messageInput.value.trim().toLowerCase(); // Convert to lowercase for matching
    if (userMessage) {
        // Add user message to chat
        addMessage(userMessage, 'user');

        // Determine bot response
        const botResponse = defaultResponses[userMessage] || "I'm not sure how to respond to that.";

        // Simulate bot response after 1 second
        setTimeout(() => {
            addMessage(botResponse, 'bot');
        }, 1000);

        // Clear input field
        messageInput.value = '';
    }
}

// Send message on button click
sendMessageButton.addEventListener('click', handleSendMessage);

// Send message on Enter key press
messageInput.addEventListener('keypress', (event) => {
    if (event.key === 'Enter') {
        handleSendMessage();
        event.preventDefault(); // Prevent form submission if inside a form
    }
});

function addMessage(text, type) {
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('message', type);
    messageDiv.textContent = text;
    messagesDiv.appendChild(messageDiv);
    messagesDiv.scrollTop = messagesDiv.scrollHeight; // Scroll to the bottom
}

</script>


</body>
</html>
