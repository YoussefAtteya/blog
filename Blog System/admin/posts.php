<?php
include "inc/config.php";
session_start();
requireAdmin();

// Handle post actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $post_id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE posts SET status = 'published' WHERE id = ?");
        $stmt->execute([$post_id]);
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE posts SET status = 'draft' WHERE id = ?");
        $stmt->execute([$post_id]);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
    }
    
    header("Location: posts.php");
    exit();
}

// Get all posts with user and category info
$stmt = $pdo->query("
    SELECT p.*, u.username, c.name as category_name 
    FROM posts p 
    LEFT JOIN users u ON p.user_id = u.id 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.created_at DESC
");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Manage Posts";
$additionalCSS = "
<style>
.admin-main-content {
    margin-left: 320px;
    margin-right: 20px;
    padding: 30px;
    min-height: calc(100vh - 160px);
    transition: var(--transition);
}

.posts-container {
    max-width: 1400px;
    margin: 0 auto;
}

.posts-title {
    font-family: 'Pacifico', cursive;
    font-size: 3rem;
    margin-bottom: 40px;
    background: linear-gradient(90deg, #fff, var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-align: center;
}

.posts-table {
    background: var(--glass-bg);
    backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--glass-shadow);
}

.table-header {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1fr 2fr;
    gap: 15px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.1);
    border-bottom: 1px solid var(--glass-border);
    font-weight: 600;
    color: var(--text-primary);
}

.post-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1fr 2fr;
    gap: 15px;
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: var(--transition);
}

.post-row:hover {
    background: rgba(255, 255, 255, 0.05);
}

.post-row:last-child {
    border-bottom: none;
}

.post-title {
    font-weight: 500;
    color: var(--text-primary);
}

.post-author, .post-category, .post-date {
    color: var(--text-secondary);
}

.status-badge {
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
    text-align: center;
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

.post-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.action-btn {
    padding: 6px 12px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
    transition: var(--transition);
    border: none;
    cursor: pointer;
}

.btn-approve {
    background: rgba(40, 167, 69, 0.2);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.btn-reject {
    background: rgba(255, 193, 7, 0.2);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.btn-delete {
    background: rgba(220, 53, 69, 0.2);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.3);
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.no-posts {
    text-align: center;
    padding: 60px 40px;
    color: var(--text-secondary);
}

.no-posts i {
    font-size: 4rem;
    margin-bottom: 20px;
    color: var(--text-secondary);
}

.admin-circle {
    position: fixed;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
    z-index: -1;
    animation: float-home 8s ease-in-out infinite;
}

.admin-circle.pc1 { width: 120px; height: 120px; top: 15%; right: 8%; animation-delay: 0s; }
.admin-circle.pc2 { width: 80px; height: 80px; bottom: 20%; left: 60%; animation-delay: 2s; }

@media (max-width: 1200px) {
    .admin-main-content {
        margin-left: 20px;
        margin-right: 20px;
    }
}

@media (max-width: 768px) {
    .posts-title {
        font-size: 2.5rem;
    }
    
    .table-header {
        display: none;
    }
    
    .post-row {
        grid-template-columns: 1fr;
        gap: 10px;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.05);
    }
    
    .post-actions {
        justify-content: center;
        margin-top: 10px;
    }
}
</style>
";

include "../inc/header.php";
// Include your existing navigation (unchanged)
include "inc/nav_admin.php";
// Include the new admin sidebar
include "inc/sidebar.php";
?>

<div class="admin-main-content">
    <div class="posts-container">
        <!-- Floating circles -->
        <div class="admin-circle pc1"></div>
        <div class="admin-circle pc2"></div>

        <h1 class="posts-title">Manage Posts</h1>
        
        <div class="posts-table">
            <div class="table-header">
                <div>Title</div>
                <div>Author</div>
                <div>Category</div>
                <div>Status</div>
                <div>Date</div>
                <div>Actions</div>
            </div>
            
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                <div class="post-row">
                    <div class="post-title"><?php echo htmlspecialchars($post['title']); ?></div>
                    <div class="post-author"><?php echo htmlspecialchars($post['username']); ?></div>
                    <div class="post-category"><?php echo htmlspecialchars($post['category_name']); ?></div>
                    <div>
                        <span class="status-badge status-<?php echo $post['status']; ?>">
                            <?php echo ucfirst($post['status']); ?>
                        </span>
                    </div>
                    <div class="post-date"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></div>
                    <div class="post-actions">
                        <?php if ($post['status'] === 'pending'): ?>
                            <a href="posts.php?action=approve&id=<?php echo $post['id']; ?>" class="action-btn btn-approve">Approve</a>
                            <a href="posts.php?action=reject&id=<?php echo $post['id']; ?>" class="action-btn btn-reject">Reject</a>
                        <?php endif; ?>
                        <a href="posts.php?action=delete&id=<?php echo $post['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this post?')">Delete</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-posts">
                    <i class="fas fa-file-alt"></i>
                    <h3>No Posts Found</h3>
                    <p>There are no posts in the system yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../inc/footer.php"; ?>