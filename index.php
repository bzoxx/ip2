<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/png" href="images/logo.png">
    <title>Blind Cupid</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Header -->
    <header>
        <div class="logo">
            <h1 ><a href="index.php" id="Blind" aria-label="Go to Blind Cupid Home">Blind Cupid</a></h1>
        </div><!-- where is it -->
        <nav>
            <ul>
                <li><a href="index.php" aria-label="Go to Home">Home</a></li>
                <li><a href="signup.php" aria-label="Sign Up">Sign Up</a></li>
                <li><a href="login.php" aria-label="Login to your account">Login</a></li>
                <li><a href="about.php" aria-label="Learn How Blind Date Hub Works">About</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content with 3 sections hero,fetures,testimonals -->
    <main>
        <section class="hero">
            <div class="hero-content">
                <h1><?php echo "Welcome to Blind Cupid" ?></h1>
                <p>Find your match through meaningful conversations. No photos, just connections!</p>
                <a href="signup.html" class="cta-button">Get Started</a>
            </div>
            <div class="hero-image">
                <img src="images/hero-dating.webp" alt="Two phones" />
            </div>
        </section>

        <section class="features">
            <h2>Why Blind Cupid?</h2>
            <div class="feature-cards">
                <div class="card">
                    <img src="images/feature1.webp" alt="Private Conversations" />
                    <h3>Private Conversations</h3>
                    <p>Connect securely with others without revealing your identity.</p>
                </div>
                <div class="card">
                    <img src="images/feature2.webp" alt="Smart Matchmaking" />
                    <h3>Smart Matchmaking</h3>
                    <p>Our algorithm pairs you with like-minded individuals.</p>
                </div>
                <div class="card">
                    <img src="images/feature3.webp" alt="No Judgments" />
                    <h3>No Judgments</h3>
                    <p>It's all about who you are, not what you look like.</p>
                </div>
            </div>
        </section>
   </main>
<!-- Footer -->
    <footer>
        <section class="testimonials">
            <h2>What Our Users Say</h2>
            <div class="testimonial">
                <p><?php echo "Blind Cupid helped me find someone who truly understands me. The no-photo approach is a game changer!"?></p>
                <span>- Demeke</span>
            </div>
            <div class="testimonial">
                <p><?php echo "I love how secure and private the platform is. It's all about real connections."?></p>
                <span>- Hailee</span>
            </div>
        </section>
    

    
        <p>&copy; 2024 Blind Cupid. All rights reserved.</p>
        <p>
            <a href="about.php" aria-label="Learn more about us">About</a> | 
            <a href="privacyPolicy.php" aria-label="Read our Privacy Policy">Privacy Policy</a> | 
            <a href="terms.php" aria-label="Review our Terms of Service">Terms of Service</a>
        </p>
    </footer>

</body>
</html>
