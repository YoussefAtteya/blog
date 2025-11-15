<?php
$current_page = getCurrentPage();
$pending_counts = getPendingCounts();
?>
<!-- Admin Sidebar Styles -->
<style>
.admin-sidebar {
    position: fixed;
    left: 20px;
    top: 120px;
    height: calc(100vh - 160px);
    width: 280px;
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 25px;
    padding: 30px 0;
    box-shadow: var(--glass-shadow);
    z-index: 999;
    overflow-y: auto;
    transition: var(--transition);
}

.admin-sidebar:hover {
    transform: translateX(5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
}

.admin-sidebar::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    transition: left 0.6s;
    border-radius: 25px;
}

.admin-sidebar:hover::before {
    left: 100%;
}

.sidebar-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 0 25px;
}

.sidebar-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-bottom: 15px;
}

.sidebar-logo-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #00c6ff, #0072ff);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0, 114, 255, 0.4);
    animation: pulse 2s infinite;
}

.sidebar-logo-icon::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
    transform: rotate(45deg);
    animation: shine 3s infinite;
}

.sidebar-logo-text {
    font-size: 1.6rem;
    font-weight: 700;
    background: linear-gradient(90deg, #fff, var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.sidebar-subtitle {
    color: var(--text-secondary);
    font-size: 0.9rem;
    font-weight: 500;
}

.sidebar-menu {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 0 20px;
}

.sidebar-section {
    margin-bottom: 30px;
}

.sidebar-section-title {
    color: var(--text-secondary);
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 15px;
    padding: 0 15px;
}

.sidebar-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 16px 20px;
    border-radius: 15px;
    color: var(--text-secondary);
    text-decoration: none;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    border: 1px solid transparent;
}

.sidebar-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    transition: left 0.5s;
}

.sidebar-item:hover::before {
    left: 100%;
}

.sidebar-item:hover {
    background: rgba(255, 255, 255, 0.1);
    color: var(--text-primary);
    transform: translateX(8px);
    border-color: var(--glass-border);
}

.sidebar-item.active {
    background: linear-gradient(135deg, rgba(0, 198, 255, 0.15), rgba(0, 114, 255, 0.15));
    color: var(--text-primary);
    border-color: rgba(255, 209, 102, 0.3);
    transform: translateX(8px);
    box-shadow: 0 5px 15px rgba(0, 114, 255, 0.2);
}

.sidebar-item.active::after {
    content: '';
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    width: 8px;
    height: 8px;
    background: var(--accent);
    border-radius: 50%;
    box-shadow: 0 0 15px var(--accent);
}

.sidebar-item i {
    width: 20px;
    text-align: center;
    font-size: 1.2rem;
    transition: var(--transition);
}

.sidebar-item.active i {
    color: var(--accent);
    transform: scale(1.1);
}

.sidebar-item span {
    font-weight: 500;
    font-size: 0.95rem;
    flex: 1;
}

.sidebar-badge {
    background: linear-gradient(135deg, #ff6b6b, #ee5a24);
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    min-width: 20px;
    text-align: center;
    box-shadow: 0 3px 10px rgba(255, 107, 107, 0.3);
}

.sidebar-footer {
    margin-top: auto;
    padding: 25px 20px 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    border: 1px solid var(--glass-border);
    transition: var(--transition);
}

.user-info:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.user-avatar-small {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f7971e, #ffd200);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    box-shadow: 0 4px 15px rgba(247, 151, 30, 0.4);
}

.user-details {
    flex: 1;
}

.user-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.user-role {
    font-size: 0.8rem;
    color: var(--accent);
    text-transform: capitalize;
    font-weight: 500;
}

/* Main content adjustment for sidebar */
.admin-main-content {
    margin-left: 320px;
    margin-right: 20px;
    padding: 30px;
    min-height: calc(100vh - 160px);
    transition: var(--transition);
}

/* Mobile responsive */
@media (max-width: 1200px) {
    .admin-sidebar {
        transform: translateX(-100%);
        width: 300px;
        z-index: 1001;
    }
    
    .admin-sidebar.active {
        transform: translateX(0);
    }
    
    .admin-main-content {
        margin-left: 20px;
        margin-right: 20px;
    }
    
    .sidebar-toggle {
        display: block;
        position: fixed;
        top: 90px;
        left: 20px;
        z-index: 1002;
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        color: var(--text-primary);
        padding: 12px;
        border-radius: 12px;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: var(--glass-shadow);
    }
    
    .sidebar-toggle:hover {
        transform: scale(1.1);
        background: rgba(255, 255, 255, 0.15);
    }
}

.sidebar-toggle {
    display: none;
}

/* Scrollbar styling - VERTICAL ONLY */
.admin-sidebar::-webkit-scrollbar {
    width: 6px;
}

.admin-sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
}

.admin-sidebar::-webkit-scrollbar-thumb {
    background: var(--accent);
    border-radius: 3px;
}

.admin-sidebar::-webkit-scrollbar-thumb:hover {
    background: #ffc233;
}

/* Prevent horizontal scrolling */
.admin-sidebar {
    overflow-x: hidden;
}

.sidebar-menu {
    width: 100%;
}

.sidebar-item {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Category badge preview in sidebar */
.category-badge-preview {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-left: auto;
}

.badge-preview-primary { background: #0072ff; }
.badge-preview-success { background: #28a745; }
.badge-preview-warning { background: #ffc107; }
.badge-preview-danger { background: #dc3545; }
.badge-preview-info { background: #17a2b8; }
.badge-preview-secondary { background: #6c757d; }
.badge-preview-dark { background: #343a40; }
</style>

<!-- Sidebar Toggle Button (Mobile) -->
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- Admin Sidebar -->
<div class="admin-sidebar" id="adminSidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <i class="fas fa-cog"></i>
            </div>
            <div class="sidebar-logo-text">ADMIN</div>
        </div>
        <div class="sidebar-subtitle">Control Panel</div>
    </div>

    <!-- Main Menu -->
    <div class="sidebar-menu">
        <div class="sidebar-section">
            <div class="sidebar-section-title">Dashboard</div>
            <a href="dashboard.php" class="sidebar-item <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Overview</span>
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Content Management</div>
            <a href="posts.php" class="sidebar-item <?php echo $current_page === 'posts.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i>
                <span>Blog Posts</span>
                <?php if ($pending_counts['posts'] > 0): ?>
                <span class="sidebar-badge"><?php echo $pending_counts['posts']; ?></span>
                <?php endif; ?>
            </a>
            <a href="categories.php" class="sidebar-item <?php echo $current_page === 'categories.php' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
                <?php
                // Get total categories count for badge
                $categories_count = 0;
                try {
                    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
                    $categories_count = $stmt->fetchColumn();
                } catch (PDOException $e) {
                    // Handle error silently
                }
                ?>
                <?php if ($categories_count > 0): ?>
                <span class="sidebar-badge" style="background: linear-gradient(135deg, #00c6ff, #0072ff);"><?php echo $categories_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="comments.php" class="sidebar-item <?php echo $current_page === 'comments.php' ? 'active' : ''; ?>">
                <i class="fas fa-comments"></i>
                <span>Comments</span>
                <?php if ($pending_counts['comments'] > 0): ?>
                <span class="sidebar-badge"><?php echo $pending_counts['comments']; ?></span>
                <?php endif; ?>
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">User Management</div>
            <a href="users.php" class="sidebar-item <?php echo $current_page === 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users-cog"></i>
                <span>Manage Users</span>
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Quick Actions</div>
            <a href="../home.php" class="sidebar-item">
                <i class="fas fa-external-link-alt"></i>
                <span>View Site</span>
            </a>
            <a href="../create.php" class="sidebar-item">
                <i class="fas fa-plus-circle"></i>
                <span>New Post</span>
            </a>
        </div>

       
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar-small">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="user-details">
                <div class="user-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Administrator'); ?></div>
                <div class="user-role"><?php echo htmlspecialchars($_SESSION['role'] ?? 'admin'); ?></div>
            </div>
        </div>
    </div>
</div>

<script>
// Sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar = document.getElementById('adminSidebar');
    
    if (sidebarToggle && adminSidebar) {
        sidebarToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 1200) {
                if (!adminSidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    adminSidebar.classList.remove('active');
                }
            }
        });
        
        // Auto-close sidebar on mobile when clicking a link
        const sidebarLinks = document.querySelectorAll('.sidebar-item');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 1200) {
                    adminSidebar.classList.remove('active');
                }
            });
        });
    }
});
</script>