<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interest Survey - Blind Cupid</title>
    <link rel="stylesheet" href="css/interest.css">
    <link rel="icon" type="image/png" href="images/logo.png">

</head>
<body>

    <!-- Header Section -->
    <header>
        <div class="logo">
            <h1>Blind Cupid</h1>
        </div>
        <nav>
            <ul>
                <li><a href="dashboard.html" aria-label="Go to Dashboard">Dashboard</a></li>
                <li><a href="profile.html" aria-label="View your Profile">Profile</a></li>
                <li><a href="settings.html" aria-label="Go to Settings">Settings</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content Section -->
    <main>
        <h1>Tell Us About Yourself</h1>
        <p class="description">Answer the following questions to help us match you with compatible individuals. Use the sliders to indicate your preferences.</p>

        <!-- Survey Form -->
        <form>
            <fieldset>
                <legend>Personal Preferences</legend>

                <!-- Music Preference Question -->
                <div class="question-group">
                    <label>What type of music do you enjoy? (Select all that apply)</label>
                    <div class="emoji-selection">
                        <button type="button" class="emoji-option">🎸 Pop</button>
                        <button type="button" class="emoji-option">🎷 Jazz</button>
                        <button type="button" class="emoji-option">🎤 Rap</button>
                        <button type="button" class="emoji-option">🎻 Classical</button>
                        <button type="button" class="emoji-option">🎶 Other</button>
                    </div>
                </div>

                <!-- Movie Preference Question -->
                <div class="question-group">
                    <label>What type of movies do you prefer? (Select all that apply)</label>
                    <div class="emoji-selection">
                        <button type="button" class="emoji-option">🎥 Action</button>
                        <button type="button" class="emoji-option">😂 Comedy</button>
                        <button type="button" class="emoji-option">💖 Romance</button>
                        <button type="button" class="emoji-option">👻 Horror</button>
                        <button type="button" class="emoji-option">🌌 Sci-fi</button>
                    </div>
                </div>

                <!-- Dream Saturday Night Question -->
                <div class="question-group">
                    <label>What’s your dream Saturday night?</label>
                    <div class="emoji-selection">
                        <button type="button" class="emoji-option">🍷 A romantic dinner</button>
                        <button type="button" class="emoji-option">🎉 Partying with friends</button>
                        <button type="button" class="emoji-option">🎬 Watching movies at home</button>
                        <button type="button" class="emoji-option">🌍 Exploring new places or activities</button>
                    </div>
                </div>

                <!-- Spirit Animal Question -->
                <div class="question-group">
                    <label>What’s your spirit animal?</label>
                    <div class="emoji-selection">
                        <button type="button" class="emoji-option">🦁 A lion (bold and fierce)</button>
                        <button type="button" class="emoji-option">🐬 A dolphin (playful and smart)</button>
                        <button type="button" class="emoji-option">🐼 A panda (chill and cuddly)</button>
                        <button type="button" class="emoji-option">🦉 An owl (wise and thoughtful)</button>
                    </div>
                </div>

                <!-- Love Language Question -->
                <div class="question-group">
                    <label>What’s your favorite way to show love?</label>
                    <div class="emoji-selection">
                        <button type="button" class="emoji-option">💬 Words of affirmation</button>
                        <button type="button" class="emoji-option">⏳ Quality time</button>
                        <button type="button" class="emoji-option">🤲 Acts of service</button>
                        <button type="button" class="emoji-option">🤗 Physical touch</button>
                        <button type="button" class="emoji-option">🎁 Gift-giving</button>
                    </div>
                </div>

                <!-- Ideal Lifestyle Question -->
                <div class="question-group">
                    <label>What’s your ideal lifestyle?</label>
                    <div class="emoji-selection">
                        <button type="button" class="emoji-option">🏙 City life, fast-paced and exciting</button>
                        <button type="button" class="emoji-option">🏡 Suburban life, balanced and peaceful</button>
                        <button type="button" class="emoji-option">🌾 Rural life, quiet and simple</button>
                        <button type="button" class="emoji-option">🛫 Nomadic life, traveling often</button>
                    </div>
                </div>

                <!-- Superpower Question -->
                <div class="question-group">
                    <label>If you could have a superpower, what would it be?</label>
                    <div class="emoji-selection">
                        <button type="button" class="emoji-option">🕶️ Invisibility</button>
                        <button type="button" class="emoji-option">⚡ Super speed</button>
                        <button type="button" class="emoji-option">🦸 Flying</button>
                        <button type="button" class="emoji-option">🧠 Mind-reading</button>
                        <button type="button" class="emoji-option">💪 Super strength</button>
                    </div>
                </div>

                <!-- Submit Button -->
                <p onclick="location.href='dashboard.html'">Submit Survey</p>
            </fieldset>
        </form>

        <!-- Info Section -->
        <h2>Why This Survey Matters</h2>
        <p class="info">By answering these questions, we can better understand your personality and preferences, making it easier to find someone who aligns with your values and interests.</p>

    </main>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024 Blind Cupid. All rights reserved.</p>
        <p><a href="about.html">About</a> | <a href="privacyPolicy.html">Privacy Policy</a> | <a href="terms.html">Terms of Service</a></p>
    </footer>

    <!-- JavaScript for Emoji Selection -->
    <script>
        document.querySelectorAll('.emoji-option').forEach(button => {
            button.addEventListener('click', () => {
                button.classList.toggle('active');
            });
        });


    </script>

</body>
</html>
