<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matchmaking - Blind Cupid</title>
    <link rel="stylesheet" href="css/match.css">
    <link rel="icon" type="image/png" href="images/logo.png">

</head>
<body>

     <!-- Header -->
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

    <!-- Main Content -->
    <main>
        <section class="content">

            <!-- Potential Matches -->
            <section class="matches">
                <h2>Your Potential Matches</h2>
                <ul class="match-list">
                    <li>
                        <span>Match 1</span>
                        <button onclick="location.href='chat.html'">Request</button>
                    </li>
                    <li>
                        <span>Match 2</span>
                        <button onclick="location.href='chat.html'">Request</button>
                    </li>
                    <li>
                        <span>Match 3</span>
                        <button onclick="location.href='chat.html'">Request</button>
                    </li>
                </ul>
            </section>
        </section>

        <!-- Notifications Sidebar -->
        <aside class="notifications">
            <h2>Pending Matches</h2>
            <ul class="pending-list">
                <li>
                    Match Request from Jane <button>Accept</button> <button>Decline</button>
                </li>
                <li>
                    Match Request from John <button>Accept</button> <button>Decline</button>
                </li>
            </ul>
        </aside>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Blind Cupid. All rights reserved.</p>
        <p><a href="about.html">About</a> | <a href="privacyPolicy.html">Privacy Policy</a> | <a href="terms.html">Terms of Service</a></p>
    </footer>

</body>
</html>
