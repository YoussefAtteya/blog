<?php
include "inc/config.php";

// Start session and check if user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data from database
$user_id = $_SESSION['user_id'];
$user = null;
$posts_count = 0;
$comments_count = 0;
$total_views = 0;
$user_posts = [];
$user_comments = [];

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
    
    // Get user statistics
    try {
        $posts_stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
        $posts_stmt->execute([$user_id]);
        $posts_count = $posts_stmt->fetchColumn();
    } catch (PDOException $e) {
        $posts_count = 0;
    }
    
    try {
        $comments_stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE email = ?");
        $comments_stmt->execute([$user['email']]);
        $comments_count = $comments_stmt->fetchColumn();
    } catch (PDOException $e) {
        $comments_count = 0;
    }
    
    try {
        $views_stmt = $pdo->prepare("SELECT SUM(views) FROM posts WHERE user_id = ?");
        $views_stmt->execute([$user_id]);
        $total_views = $views_stmt->fetchColumn() ?: 0;
    } catch (PDOException $e) {
        $total_views = 0;
    }
    
    // Get user's recent posts
    try {
        $posts_stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                                   FROM posts p 
                                   LEFT JOIN categories c ON p.category_id = c.id 
                                   WHERE p.user_id = ? 
                                   ORDER BY p.created_at DESC 
                                   LIMIT 3");
        $posts_stmt->execute([$user_id]);
        $user_posts = $posts_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $user_posts = [];
    }
    
    // Get user's recent comments
    try {
        $comments_stmt = $pdo->prepare("SELECT c.*, p.title as post_title, p.id as post_id 
                                      FROM comments c 
                                      LEFT JOIN posts p ON c.post_id = p.id 
                                      WHERE c.email = ? 
                                      ORDER BY c.created_at DESC 
                                      LIMIT 5");
        $comments_stmt->execute([$user['email']]);
        $user_comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $user_comments = [];
    }
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Set page title and additional CSS
$pageTitle = "Profile | " . $user['username'];
$additionalCSS = "
    /* Profile Page Styles */
    .profile-container {
        min-height: calc(100vh - 200px);
        padding: 40px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
    }

    .profile-header {
        text-align: center;
        max-width: 800px;
        margin-bottom: 40px;
    }

    .profile-title {
        font-family: 'Pacifico', cursive;
        font-size: 3rem;
        margin-bottom: 10px;
        background: linear-gradient(90deg, #fff, var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .profile-subtitle {
        font-size: 1.2rem;
        color: var(--text-secondary);
    }

    .profile-content {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 30px;
        max-width: 1200px;
        width: 100%;
        margin-top: 20px;
    }

    /* Profile Sidebar */
    .profile-sidebar {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 25px;
        padding: 30px;
        box-shadow: var(--glass-shadow);
        text-align: center;
        height: fit-content;
        position: sticky;
        top: 30px;
    }

    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f7971e, #ffd200);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 3rem;
        color: white;
        box-shadow: 0 8px 25px rgba(247, 151, 30, 0.4);
        position: relative;
        overflow: hidden;
    }

    .profile-avatar::before {
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

    .profile-name {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 5px;
        color: var(--text-primary);
    }

    .profile-role {
        color: var(--accent);
        font-weight: 500;
        margin-bottom: 20px;
        text-transform: capitalize;
    }

    .profile-bio {
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 25px;
        font-size: 0.95rem;
    }

    .profile-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }

    .stat-box {
        text-align: center;
        padding: 15px 10px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        transition: var(--transition);
    }

    .stat-box:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-5px);
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--accent);
        display: block;
    }

    .stat-label {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .profile-actions {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .profile-btn {
        padding: 12px 20px;
        border-radius: 50px;
        font-weight: 600;
        color: #fff;
        border: none;
        transition: var(--transition);
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 0.95rem;
    }

    .profile-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .btn-edit {
        background: linear-gradient(135deg, #00c6ff, #0072ff);
        box-shadow: 0 4px 15px rgba(0, 114, 255, 0.4);
    }

    .btn-settings {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        box-shadow: 0 4px 15px rgba(106, 17, 203, 0.4);
    }

    .btn-logout {
        background: linear-gradient(135deg, #ff416c, #ff4b2b);
        box-shadow: 0 4px 15px rgba(255, 75, 43, 0.4);
    }

    /* Profile Main Content */
    .profile-main {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .profile-section {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 25px;
        padding: 30px;
        box-shadow: var(--glass-shadow);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-action {
        color: var(--accent);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .section-action:hover {
        color: #fff;
        transform: translateX(5px);
    }

    /* Recent Activity */
    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 15px;
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.05);
        transition: var(--transition);
    }

    .activity-item:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateX(5px);
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00c6ff, #0072ff);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
    }

    .activity-text {
        color: var(--text-primary);
        margin-bottom: 5px;
        line-height: 1.4;
    }

    .activity-time {
        color: var(--text-secondary);
        font-size: 0.85rem;
    }

    /* User Posts */
    .posts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .post-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        padding: 20px;
        transition: var(--transition);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .post-card:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-5px);
    }

    .post-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
    }

    .post-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 5px;
    }

    .post-date {
        color: var(--text-secondary);
        font-size: 0.8rem;
        white-space: nowrap;
        margin-left: 10px;
    }

    .post-excerpt {
        color: var(--text-secondary);
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .post-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-published {
        background: rgba(40, 167, 69, 0.2);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.3);
    }

    .status-pending {
        background: rgba(255, 193, 7, 0.2);
        color: #ffc107;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-draft {
        background: rgba(108, 117, 125, 0.2);
        color: #6c757d;
        border: 1px solid rgba(108, 117, 125, 0.3);
    }

    .post-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    /* Comments Section */
    .comments-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .comment-item {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        padding: 20px;
        transition: var(--transition);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .comment-item:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-3px);
    }

    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
    }

    .comment-post {
        font-weight: 600;
        color: var(--accent);
        text-decoration: none;
        font-size: 1rem;
    }

    .comment-post:hover {
        color: #fff;
    }

    .comment-date {
        color: var(--text-secondary);
        font-size: 0.8rem;
        white-space: nowrap;
    }

    .comment-content {
        color: var(--text-primary);
        line-height: 1.5;
        margin-bottom: 15px;
        font-size: 0.95rem;
    }

    .comment-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-approved {
        background: rgba(40, 167, 69, 0.2);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.3);
    }

    .status-pending {
        background: rgba(255, 193, 7, 0.2);
        color: #ffc107;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-rejected {
        background: rgba(220, 53, 69, 0.2);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.3);
    }

    .comment-actions {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .comment-action {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        text-decoration: none;
        transition: var(--transition);
        border: none;
        cursor: pointer;
    }

    .btn-edit-comment {
        background: rgba(0, 123, 255, 0.2);
        color: #007bff;
        border: 1px solid rgba(0, 123, 255, 0.3);
    }

    .btn-delete-comment {
        background: rgba(220, 53, 69, 0.2);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.3);
    }

    .btn-edit-comment:hover {
        background: rgba(0, 123, 255, 0.3);
        transform: translateY(-2px);
    }

    .btn-delete-comment:hover {
        background: rgba(220, 53, 69, 0.3);
        transform: translateY(-2px);
    }

    /* Account Information */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .info-item {
        background: rgba(255, 255, 255, 0.05);
        padding: 15px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .info-item label {
        display: block;
        font-weight: 600;
        color: var(--accent);
        margin-bottom: 5px;
        font-size: 0.9rem;
    }

    .info-item p {
        color: var(--text-primary);
        margin: 0;
        font-size: 1rem;
    }

    /* Profile Circles */
    .profile-circle {
        position: fixed;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        z-index: -1;
        animation: float-home 8s ease-in-out infinite;
    }

    .profile-circle.pc1 { width: 120px; height: 120px; top: 10%; left: 5%; animation-delay: 0s; }
    .profile-circle.pc2 { width: 80px; height: 80px; top: 75%; left: 90%; animation-delay: 2s; }
    .profile-circle.pc3 { width: 60px; height: 60px; top: 85%; left: 15%; animation-delay: 4s; }
    .profile-circle.pc4 { width: 100px; height: 100px; top: 20%; right: 8%; animation-delay: 1s; }

    /* Responsive Design */
    @media (max-width: 992px) {
        .profile-content {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .profile-sidebar {
            position: static;
            text-align: center;
        }
        
        .profile-stats {
            grid-template-columns: repeat(3, 1fr);
        }
        
        .profile-actions {
            flex-direction: row;
            justify-content: center;
        }
        
        .profile-btn {
            flex: 1;
            min-width: 120px;
        }
    }

    @media (max-width: 768px) {
        .profile-title {
            font-size: 2.5rem;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            font-size: 2.5rem;
        }
        
        .profile-stats {
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        
        .posts-grid {
            grid-template-columns: 1fr;
        }
        
        .profile-actions {
            flex-direction: column;
        }
        
        .comment-header {
            flex-direction: column;
            gap: 5px;
        }
        
        .comment-date {
            align-self: flex-start;
        }
    }

    @media (max-width: 480px) {
        .profile-container {
            padding: 20px 15px;
        }
        
        .profile-title {
            font-size: 2rem;
        }
        
        .profile-sidebar,
        .profile-section {
            padding: 20px;
        }
        
        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .section-action {
            align-self: flex-end;
        }
        
        .comment-actions {
            flex-direction: column;
        }
    }
";

include "inc/header.php";
include "inc/nav.php";
?>

<!-- Profile Page Content -->
<div class="profile-container">
    <!-- Additional floating circles for profile page -->
    <div class="profile-circle pc1"></div>
    <div class="profile-circle pc2"></div>
    <div class="profile-circle pc3"></div>
    <div class="profile-circle pc4"></div>

    <!-- Profile Header -->
    <div class="profile-header">
        <h1 class="profile-title">User Profile</h1>
        <p class="profile-subtitle">Manage your account and view your activity</p>
    </div>

    <!-- Profile Content -->
    <div class="profile-content">
        <!-- Profile Sidebar -->
        <div class="profile-sidebar">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h2 class="profile-name"><?php echo htmlspecialchars($user['username']); ?></h2>
            <p class="profile-role"><?php echo htmlspecialchars($user['role']); ?></p>
            <p class="profile-bio">Welcome to your profile! Here you can manage your account settings, view your blog posts, and track your activity.</p>
            
            <!-- User Stats -->
            <div class="profile-stats">
                <div class="stat-box">
                    <span class="stat-number"><?php echo $posts_count; ?></span>
                    <span class="stat-label">Posts</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number"><?php echo $comments_count; ?></span>
                    <span class="stat-label">Comments</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number"><?php echo $total_views; ?></span>
                    <span class="stat-label">Views</span>
                </div>
            </div>
            
            <!-- Profile Actions -->
            <div class="profile-actions">
                <a href="edit_profile.php" class="profile-btn btn-edit">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
               
                <a href="logout.php" class="profile-btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Profile Main Content -->
        <div class="profile-main">
            <!-- Recent Activity Section -->
            <div class="profile-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-history"></i> Recent Activity
                    </h3>
                  
                </div>
                
                <div class="activity-list">
                    <!-- Sample Activity Items -->
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="activity-content">
                            <p class="activity-text">You created a new blog post</p>
                            <span class="activity-time">Just now</span>
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-comment"></i>
                        </div>
                        <div class="activity-content">
                            <p class="activity-text">You commented on a blog post</p>
                            <span class="activity-time">2 days ago</span>
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="activity-content">
                            <p class="activity-text">You updated your profile information</p>
                            <span class="activity-time">1 week ago</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Posts Section -->
            <div class="profile-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-file-text"></i> My Blog Posts
                    </h3>
                  
                </div>
                
                <div class="posts-grid">
                    <?php if (count($user_posts) > 0): ?>
                        <?php foreach ($user_posts as $post): 
                            $excerpt = strip_tags($post['content']);
                            if (strlen($excerpt) > 100) {
                                $excerpt = substr($excerpt, 0, 100) . '...';
                            }
                        ?>
                            <div class="post-card">
                                <div class="post-header">
                                    <h4 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h4>
                                    <span class="post-status status-<?php echo $post['status']; ?>">
                                        <?php echo ucfirst($post['status']); ?>
                                    </span>
                                </div>
                                <p class="post-excerpt"><?php echo $excerpt; ?></p>
                                <div class="post-meta">
                                    <span class="post-date"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                                    <span class="post-views"><i class="fas fa-eye"></i> <?php echo $post['views'] ?? 0; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-posts" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                            <i class="fas fa-file-alt" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 15px;"></i>
                            <h4 style="color: var(--text-primary); margin-bottom: 10px;">No Posts Yet</h4>
                            <p style="color: var(--text-secondary); margin-bottom: 20px;">You haven't created any blog posts yet.</p>
                            <a href="create.php" class="profile-btn btn-edit" style="display: inline-flex;">
                                <i class="fas fa-plus"></i> Create Your First Post
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Comments Section -->
            <div class="profile-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-comments"></i> My Comments
                    </h3>
                  
                </div>
                
                <div class="comments-list">
                    <?php if (count($user_comments) > 0): ?>
                        <?php foreach ($user_comments as $comment): ?>
                            <div class="comment-item">
                                <div class="comment-header">
                                    <a href="post.php?id=<?php echo $comment['post_id']; ?>" class="comment-post">
                                        <i class="fas fa-file-alt"></i> <?php echo htmlspecialchars($comment['post_title']); ?>
                                    </a>
                                    <span class="comment-date"><?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?></span>
                                </div>
                                <div class="comment-content">
                                    <?php echo htmlspecialchars($comment['comment']); ?>
                                </div>
                                <div class="comment-footer">
                                    <span class="comment-status status-<?php echo $comment['status']; ?>">
                                        <?php echo ucfirst($comment['status']); ?>
                                    </span>
                                    
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-comments" style="text-align: center; padding: 40px;">
                            <i class="fas fa-comments" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 15px;"></i>
                            <h4 style="color: var(--text-primary); margin-bottom: 10px;">No Comments Yet</h4>
                            <p style="color: var(--text-secondary;">You haven't posted any comments yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Account Information Section -->
            <div class="profile-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i> Account Information
                    </h3>
                </div>
                
                <div class="account-info">
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Username</label>
                            <p><?php echo htmlspecialchars($user['username']); ?></p>
                        </div>
                        <div class="info-item">
                            <label>Email</label>
                            <p><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                        <div class="info-item">
                            <label>Role</label>
                            <p><?php echo ucfirst(htmlspecialchars($user['role'])); ?></p>
                        </div>
                        <div class="info-item">
                            <label>Member Since</label>
                            <p><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Add interactive elements to profile page
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to profile cards
        const profileCards = document.querySelectorAll('.profile-section, .profile-sidebar, .post-card, .activity-item, .stat-box, .comment-item');
        
        profileCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Add animation to stats on page load
        const statNumbers = document.querySelectorAll('.stat-number');
        statNumbers.forEach(stat => {
            const finalValue = parseInt(stat.textContent);
            let currentValue = 0;
            const increment = finalValue / 30;
            const timer = setInterval(() => {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    stat.textContent = finalValue;
                    clearInterval(timer);
                } else {
                    stat.textContent = Math.floor(currentValue);
                }
            }, 50);
        });
    });
</script>

<?php include "inc/footer.php"; ?>