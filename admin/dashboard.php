<?php
include "inc/config.php";
session_start();
requireAdmin();

// Get stats
$total_posts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pending_posts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'pending'")->fetchColumn();
$pending_comments = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();

$pageTitle = "Admin Dashboard";
$additionalCSS = "
<style>
.admin-main-content {
    margin-left: 320px;
    margin-right: 20px;
    padding: 30px;
    min-height: calc(100vh - 160px);
    transition: var(--transition);
}

.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-title {
    font-family: 'Pacifico', cursive;
    font-size: 3rem;
    margin-bottom: 40px;
    background: linear-gradient(90deg, #fff, var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-align: center;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 50px;
}

.stat-card {
    background: var(--glass-bg);
    backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 35px 30px;
    text-align: center;
    transition: var(--transition);
    box-shadow: var(--glass-shadow);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    transition: left 0.6s;
}

.stat-card:hover::before {
    left: 100%;
}

.stat-card:hover {
    transform: translateY(-10px);
    background: rgba(255, 255, 255, 0.15);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

.stat-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 20px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.stat-icon.posts { background: linear-gradient(135deg, #00c6ff, #0072ff); }
.stat-icon.users { background: linear-gradient(135deg, #f7971e, #ffd200); }
.stat-icon.pending { background: linear-gradient(135deg, #ff6b6b, #ee5a24); }
.stat-icon.comments { background: linear-gradient(135deg, #00b09b, #96c93d); }

.stat-number {
    font-size: 3.5rem;
    font-weight: 700;
    background: linear-gradient(90deg, #fff, var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 10px;
    line-height: 1;
}

.stat-label {
    color: var(--text-secondary);
    font-size: 1.1rem;
    font-weight: 500;
}

.admin-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    max-width: 1000px;
    margin: 0 auto;
}

.btn-admin {
    background: var(--glass-bg);
    backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 30px 25px;
    text-align: center;
    text-decoration: none;
    color: var(--text-primary);
    font-weight: 600;
    font-size: 1.1rem;
    transition: var(--transition);
    box-shadow: var(--glass-shadow);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
    position: relative;
    overflow: hidden;
}

.btn-admin::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    transition: left 0.6s;
}

.btn-admin:hover::before {
    left: 100%;
}

.btn-admin:hover {
    transform: translateY(-8px);
    background: rgba(255, 255, 255, 0.15);
    color: var(--accent);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
}

.btn-admin i {
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.admin-circle {
    position: fixed;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
    z-index: -1;
    animation: float-home 8s ease-in-out infinite;
}

.admin-circle.ac1 { width: 120px; height: 120px; top: 15%; right: 8%; animation-delay: 0s; }
.admin-circle.ac2 { width: 80px; height: 80px; bottom: 20%; left: 60%; animation-delay: 2s; }
.admin-circle.ac3 { width: 60px; height: 60px; top: 60%; right: 20%; animation-delay: 4s; }

@media (max-width: 1200px) {
    .admin-main-content {
        margin-left: 20px;
        margin-right: 20px;
    }
}

@media (max-width: 768px) {
    .dashboard-title {
        font-size: 2.5rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .admin-actions {
        grid-template-columns: 1fr;
    }
    
    .stat-card {
        padding: 25px 20px;
    }
    
    .btn-admin {
        padding: 25px 20px;
    }
}
</style>
";

include "../inc/header.php";
// Include both admin navigation and sidebar
include "inc/nav_admin.php";
include "inc/sidebar.php";
?>

<div class="admin-main-content">
    <div class="dashboard-container">
        <!-- Floating circles -->
        <div class="admin-circle ac1"></div>
        <div class="admin-circle ac2"></div>
        <div class="admin-circle ac3"></div>

        <h1 class="dashboard-title">Admin Dashboard</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon posts">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-number"><?php echo $total_posts; ?></div>
                <div class="stat-label">Total Posts</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?php echo $pending_posts; ?></div>
                <div class="stat-label">Pending Posts</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon comments">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-number"><?php echo $pending_comments; ?></div>
                <div class="stat-label">Pending Comments</div>
            </div>
        </div>

        <div class="admin-actions">
            <a href="posts.php" class="btn-admin">
                <i class="fas fa-file-alt"></i>
                <span>Manage Posts</span>
                <small>Approve, edit, or delete blog posts</small>
            </a>
            <a href="comments.php" class="btn-admin">
                <i class="fas fa-comments"></i>
                <span>Manage Comments</span>
                <small>Moderate user comments</small>
            </a>
            <a href="users.php" class="btn-admin">
                <i class="fas fa-users-cog"></i>
                <span>Manage Users</span>
                <small>User roles and permissions</small>
            </a>
        </div>
    </div>
</div>

<?php include "../inc/footer.php"; ?>