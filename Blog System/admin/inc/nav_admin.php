<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Navigation Container -->
<div class="nav-container">
    <!-- Enhanced Creative Navigation -->
    <nav class="navbar-main"  id="navbarMain">
        <!--  PART 1: Logo Section -->
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fas fa-cube"></i>
            </div>
            <div class="logo-text">
                <div class="logo-main">MY BLOG</div>
            </div>
        </div>
        
        <!--  PART 2: Desktop Navigation Menu -->
        <ul class="nav-links">



            <li>
                <a href="../home.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home nav-icon"></i>
                    <span>Home</span>
                </a>
            </li>
            <li>
                <a href="../about.php"class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">
                    <i class="fas fa-info-circle nav-icon"></i>
                    <span>About</span>
                </a>
            </li>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
                <li>
                    <a href="dashboard.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
              
            <?php endif; ?>




        </ul>

        <!--  PART 3: Desktop User Section -->
        <div class="user-section">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="auth-btn btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </a>
            <?php else: ?>
                <div class="user-avatar">
                <a href="../user.php"><i class="fas fa-user"></i></a>
                </div>
                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'user'): ?>
                    <a href="../logout.php" class="auth-btn btn-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Mobile Toggle Button -->
        <button class="nav-toggle" id="navToggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>

    <!-- Fixed Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <a href="../home.php" class="mobile-nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
         <a href="../about.php" class="mobile-nav-item">
                <i class="fas fa-info-circle"></i>
                <span>About</span>
            </a>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
            <a href="dashboard.php" class="mobile-nav-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
         
        <?php endif; ?>

        <!-- Logout Button in Mobile Menu -->
        <?php if (isset($_SESSION['user_id']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'user')): ?>
            <a href="../logout.php" class="mobile-nav-item mobile-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        <?php elseif(!isset($_SESSION['user_id'])): ?>
            <a href="../login.php" class="mobile-nav-item" style="background: linear-gradient(135deg, #00c6ff, #0072ff); justify-content: center; font-weight: 600;">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </a>
        <?php endif; ?>
    </div>
</div>

