<?php
if (!isset($_SESSION)) {
    session_start();
}

// Get current page name
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!-- Navigation Header -->
<header id="main-header">
    <div id="header-content" class="header-content">
        <div id="header-logo" class="logo">
            <a href="dashboard.php">
                <i class="fas fa-heart"></i>
                <h1 id="site-title">Blind Date Hub</h1>
            </a>
        </div>
        <button id="menu-toggle" class="menu-toggle" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <nav id="main-nav">
            <ul id="nav-list">
                <li class="nav-item">
                    <a href="dashboard.php" <?= $current_page === 'dashboard' ? 'class="active"' : '' ?>>
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="profile.php" <?= $current_page === 'profile' ? 'class="active"' : '' ?>>
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="chatlist.php" <?= $current_page === 'chatlist' ? 'class="active"' : '' ?>>
                        <i class="fas fa-comments"></i>
                        <span>Messages</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="settings.php" <?= $current_page === 'settings' ? 'class="active"' : '' ?>>
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</header> 
