<?php
include "inc/config.php";
session_start();
requireAdmin();

// Handle comment actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $comment_id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id = ?");
        $stmt->execute([$comment_id]);
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE comments SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$comment_id]);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$comment_id]);
    }
    
    header("Location: comments.php");
    exit();
}

// Get all comments with post info
$stmt = $pdo->query("
    SELECT c.*, p.title as post_title 
    FROM comments c 
    LEFT JOIN posts p ON c.post_id = p.id 
    ORDER BY c.created_at DESC
");
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Manage Comments";
$additionalCSS = "
<style>
.admin-main-content {
    margin-left: 320px;
    margin-right: 20px;
    padding: 30px;
    min-height: calc(100vh - 160px);
    transition: var(--transition);
}

.comments-container {
    max-width: 1400px;
    margin: 0 auto;
}

.comments-title {
    font-family: 'Pacifico', cursive;
    font-size: 3rem;
    margin-bottom: 40px;
    background: linear-gradient(90deg, #fff, var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-align: center;
}

.comments-table {
    background: var(--glass-bg);
    backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--glass-shadow);
}

.table-header {
    display: grid;
    grid-template-columns: 1fr 1fr 2fr 1fr 1fr 2fr;
    gap: 15px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.1);
    border-bottom: 1px solid var(--glass-border);
    font-weight: 600;
    color: var(--text-primary);
}

.comment-row {
    display: grid;
    grid-template-columns: 1fr 1fr 2fr 1fr 1fr 2fr;
    gap: 15px;
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: var(--transition);
}

.comment-row:hover {
    background: rgba(255, 255, 255, 0.05);
}

.comment-row:last-child {
    border-bottom: none;
}

.comment-content {
    color: var(--text-primary);
    line-height: 1.4;
}

.comment-post {
    color: var(--accent);
    font-weight: 500;
}

.comment-author, .comment-date {
    color: var(--text-secondary);
}

.status-badge {
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
    text-align: center;
}

.status-pending {
    background: rgba(255, 193, 7, 0.2);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.status-approved {
    background: rgba(40, 167, 69, 0.2);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.status-rejected {
    background: rgba(220, 53, 69, 0.2);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.3);
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

.admin-circle.cc1 { width: 120px; height: 120px; top: 15%; right: 8%; animation-delay: 0s; }
.admin-circle.cc2 { width: 80px; height: 80px; bottom: 20%; left: 60%; animation-delay: 2s; }

@media (max-width: 1200px) {
    .admin-main-content {
        margin-left: 20px;
        margin-right: 20px;
    }
}

@media (max-width: 768px) {
    .comments-title {
        font-size: 2.5rem;
    }
    
    .table-header {
        display: none;
    }
    
    .comment-row {
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
    <div class="comments-container">
        <!-- Floating circles -->
        <div class="admin-circle cc1"></div>
        <div class="admin-circle cc2"></div>

        <h1 class="comments-title">Manage Comments</h1>
        
        <div class="comments-table">
            <div class="table-header">
                <div>Name</div>
                <div>Email</div>
                <div>Comment</div>
                <div>Post</div>
                <div>Status</div>
                <div>Actions</div>
            </div>
            
            <?php if (count($comments) > 0): ?>
                <?php foreach ($comments as $comment): ?>
                <div class="comment-row">
                    <div class="comment-author"><?php echo htmlspecialchars($comment['name']); ?></div>
                    <div class="comment-author"><?php echo htmlspecialchars($comment['email']); ?></div>
                    <div class="comment-content"><?php echo htmlspecialchars($comment['comment']); ?></div>
                    <div class="comment-post"><?php echo htmlspecialchars($comment['post_title']); ?></div>
                    <div>
                        <span class="status-badge status-<?php echo $comment['status']; ?>">
                            <?php echo ucfirst($comment['status']); ?>
                        </span>
                    </div>
                    <div class="post-actions">
                        <?php if ($comment['status'] !== 'approved'): ?>
                            <a href="comments.php?action=approve&id=<?php echo $comment['id']; ?>" class="action-btn btn-approve">Approve</a>
                        <?php endif; ?>
                        <?php if ($comment['status'] !== 'rejected'): ?>
                            <a href="comments.php?action=reject&id=<?php echo $comment['id']; ?>" class="action-btn btn-reject">Reject</a>
                        <?php endif; ?>
                        <a href="comments.php?action=delete&id=<?php echo $comment['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this comment?')">Delete</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-posts">
                    <i class="fas fa-comments"></i>
                    <h3>No Comments Found</h3>
                    <p>There are no comments in the system yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../inc/footer.php"; ?>