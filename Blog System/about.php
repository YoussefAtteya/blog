
<?php
include "inc/config.php";

// Set page title and additional CSS
$pageTitle = "About | MY BLOG";
$additionalCSS = "
    /* About Page Styles */
    .about-container {
        min-height: calc(100vh - 200px);
        padding: 60px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .about-header {
        text-align: center;
        margin-bottom: 50px;
        max-width: 800px;
    }

    .about-title {
        font-size: 3.5rem;
        font-weight: 700;
        background: linear-gradient(90deg, #fff, var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 20px;
    }

    .about-subtitle {
        font-size: 1.2rem;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
        max-width: 1200px;
        width: 100%;
        margin-bottom: 60px;
    }

    .feature-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 40px 30px;
        text-align: center;
        transition: var(--transition);
        box-shadow: var(--glass-shadow);
    }

    .feature-card:hover {
        transform: translateY(-10px);
        background: rgba(255, 255, 255, 0.15);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #00c6ff, #0072ff);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        box-shadow: 0 8px 25px rgba(0, 114, 255, 0.3);
    }

    .feature-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--text-primary);
    }

    .feature-description {
        color: var(--text-secondary);
        line-height: 1.6;
        font-size: 1rem;
    }

    .stats-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        max-width: 1000px;
        width: 100%;
        margin-bottom: 60px;
    }

    .stat-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        transition: var(--transition);
    }

    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        background: linear-gradient(90deg, #fff, var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 10px;
    }

    .stat-label {
        color: var(--text-secondary);
        font-size: 1.1rem;
        font-weight: 500;
    }

    .team-section {
        max-width: 1000px;
        width: 100%;
        text-align: center;
    }

    .team-title {
        font-size: 2.5rem;
        margin-bottom: 40px;
        background: linear-gradient(90deg, #fff, var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }

    .team-member {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 30px;
        transition: var(--transition);
    }

    .team-member:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.15);
    }

    .member-avatar {
        width: 100px;
        height: 100px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #f7971e, #ffd200);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        box-shadow: 0 8px 25px rgba(247, 151, 30, 0.3);
    }

    .member-name {
        font-size: 1.4rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--text-primary);
    }

    .member-role {
        color: var(--accent);
        font-weight: 500;
        margin-bottom: 15px;
    }

    .member-bio {
        color: var(--text-secondary);
        line-height: 1.6;
        font-size: 0.95rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .about-title {
            font-size: 2.5rem;
        }
        
        .features-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .feature-card {
            padding: 30px 20px;
        }
        
        .stats-section {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .team-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .about-container {
            padding: 40px 15px;
        }
        
        .about-title {
            font-size: 2rem;
        }
        
        .stats-section {
            grid-template-columns: 1fr;
        }
        
        .stat-number {
            font-size: 2.5rem;
        }
    }
";

include "inc/header.php";
include "inc/nav.php";
?>

<!-- About Page Content -->
<div class="about-container">
    <!-- Header Section -->
    <div class="about-header">
        <h1 class="about-title">About My Blog</h1>
        <p class="about-subtitle">
            A modern, secure, and elegant platform designed to streamline your workflow and 
            enhance productivity with cutting-edge technology and user-friendly design.
        </p>
    </div>

    <!-- Features Grid -->
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3 class="feature-title">Secure & Reliable</h3>
            <p class="feature-description">
                Enterprise-grade security with end-to-end encryption and robust authentication 
                systems to keep your data safe and protected.
            </p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <h3 class="feature-title">Lightning Fast</h3>
            <p class="feature-description">
                Optimized for performance with instant load times and seamless user experience 
                across all devices and platforms.
            </p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-users-cog"></i>
            </div>
            <h3 class="feature-title">User Management</h3>
            <p class="feature-description">
                Advanced user management system with role-based access control and comprehensive 
                administrative tools.
            </p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <h3 class="feature-title">Cloud Storage</h3>
            <p class="feature-description">
                Secure cloud storage solutions with real-time synchronization and easy file 
                management capabilities.
            </p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="feature-title">Advanced Analytics</h3>
            <p class="feature-description">
                Comprehensive analytics dashboard with real-time insights and customizable 
                reporting features.
            </p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-mobile-alt"></i>
            </div>
            <h3 class="feature-title">Mobile Friendly</h3>
            <p class="feature-description">
                Fully responsive design that works perfectly on all devices, from desktop 
                computers to mobile phones.
            </p>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="stats-section">
        <div class="stat-card">
            <div class="stat-number">10K+</div>
            <div class="stat-label">Active Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">99.9%</div>
            <div class="stat-label">Uptime</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">24/7</div>
            <div class="stat-label">Support</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">50+</div>
            <div class="stat-label">Countries</div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="team-section">
        <h2 class="team-title">Our Team</h2>
        <div class="team-grid">
            <div class="team-member">
                <div class="member-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3 class="member-name">Youssef Atteya</h3>
                <div class="member-role">Founder & Lead Developer</div>
                <p class="member-bio">
                    Passionate about creating innovative solutions that make technology 
                    accessible and enjoyable for everyone.
                </p>
            </div>

          

           
        </div>
    </div>
</div>

<?php include "inc/footer.php"; ?>


