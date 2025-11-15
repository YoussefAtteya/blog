<?php
include "inc/config.php";

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get post ID from URL
$post_id = $_GET['id'] ?? 0;

// Initialize variables
$post = null;
$comments = [];
$message = "";
$message_type = "";
$user_name = "";
$user_email = "";
$user_id = null;

// Get user info if logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    try {
        $user_stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
        $user_stmt->execute([$user_id]);
        $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user_data) {
            $user_name = $user_data['username'];
            $user_email = $user_data['email'];
        }
    } catch (PDOException $e) {
        // Continue without user data
    }
}

try {
    // Get post details
    $post_stmt = $pdo->prepare("
        SELECT p.*, u.username, c.name as category_name 
        FROM posts p 
        LEFT JOIN users u ON p.user_id = u.id 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ? AND p.status = 'published'
    ");
    $post_stmt->execute([$post_id]);
    $post = $post_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        header("Location: home.php");
        exit();
    }
    
    // Increase view count
    $view_stmt = $pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
    $view_stmt->execute([$post_id]);
    
    // Get approved comments for this post with their replies
    $comments_stmt = $pdo->prepare("
        SELECT c.* 
        FROM comments c 
        WHERE c.post_id = ? AND c.status = 'approved' AND c.parent_id IS NULL
        ORDER BY c.created_at DESC
    ");
    $comments_stmt->execute([$post_id]);
    $comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get replies for each comment
    foreach ($comments as &$comment) {
        $replies_stmt = $pdo->prepare("
            SELECT r.* 
            FROM comments r 
            WHERE r.parent_id = ? AND r.status = 'approved'
            ORDER BY r.created_at ASC
        ");
        $replies_stmt->execute([$comment['id']]);
        $comment['replies'] = $replies_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    
    if (isset($_POST['add_comment'])) {
        // Main comment submission
        $name = $user_name;
        $email = $user_email;
        $comment = trim($_POST['comment']);
        $parent_id = null;

        // Validation
        if (empty($comment)) {
            $message = "Please write a comment!";
            $message_type = "danger";
        } else {
            try {
                // Insert comment
                $insert_stmt = $pdo->prepare("
                    INSERT INTO comments (post_id, name, email, comment, status, parent_id) 
                    VALUES (?, ?, ?, ?, 'pending', ?)
                ");
                $insert_stmt->execute([$post_id, $name, $email, $comment, $parent_id]);
                
                $message = "Comment submitted successfully! It will be visible after approval.";
                $message_type = "success";
                
                // Clear form field
                $_POST['comment'] = '';
                
            } catch (PDOException $e) {
                $message = "Error submitting comment: " . $e->getMessage();
                $message_type = "danger";
            }
        }
    }
    
    // Handle reply submission
    if (isset($_POST['add_reply'])) {
        $name = $user_name;
        $email = $user_email;
        $reply = trim($_POST['reply']);
        $parent_id = $_POST['parent_id'] ?? null;

        // Validation
        if (empty($reply)) {
            $message = "Please write a reply!";
            $message_type = "danger";
        } elseif (empty($parent_id)) {
            $message = "Invalid comment to reply to!";
            $message_type = "danger";
        } else {
            try {
                // Insert reply
                $insert_stmt = $pdo->prepare("
                    INSERT INTO comments (post_id, name, email, comment, status, parent_id) 
                    VALUES (?, ?, ?, ?, 'pending', ?)
                ");
                $insert_stmt->execute([$post_id, $name, $email, $reply, $parent_id]);
                
                $message = "Reply submitted successfully! It will be visible after approval.";
                $message_type = "success";
                
                // Clear reply form
                echo "<script>document.getElementById('replyForm{$parent_id}').style.display = 'none';</script>";
                
            } catch (PDOException $e) {
                $message = "Error submitting reply: " . $e->getMessage();
                $message_type = "danger";
            }
        }
    }
}

// Set page title and additional CSS
$pageTitle = $post['title'] . " | Blog System";
$additionalCSS = "
    /* Post Details Styles */
    .post-details-container {
        min-height: calc(100vh - 200px);
        padding: 40px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
    }

    .post-content {
        max-width: 900px;
        width: 100%;
        margin: 0 auto;
    }

    .post-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .post-title {
        font-family: 'Pacifico', cursive;
        font-size: 3rem;
        margin-bottom: 20px;
        background: linear-gradient(90deg, #fff, var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1.3;
    }

    .post-meta {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 30px;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .meta-item i {
        color: var(--accent);
    }

    .post-category {
        background: rgba(255, 209, 102, 0.2);
        color: var(--accent);
        padding: 6px 15px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .post-image {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
        border-radius: 20px;
        margin-bottom: 30px;
        box-shadow: var(--glass-shadow);
    }

    .post-body {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 25px;
        padding: 40px;
        box-shadow: var(--glass-shadow);
        margin-bottom: 40px;
    }

    .post-content-text {
        color: var(--text-primary);
        line-height: 1.8;
        font-size: 1.1rem;
    }

    .post-content-text p {
        margin-bottom: 20px;
    }

    .post-content-text h2, .post-content-text h3 {
        color: var(--accent);
        margin: 30px 0 15px 0;
    }

    .post-content-text ul, .post-content-text ol {
        margin: 20px 0;
        padding-left: 30px;
    }

    .post-content-text li {
        margin-bottom: 8px;
    }

    .post-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn-back {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        box-shadow: 0 4px 15px rgba(106, 17, 203, 0.4);
        color: white;
        text-decoration: none;
        padding: 12px 25px;
        border-radius: 50px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
    }

    .btn-back:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        color: white;
    }

    /* Comments Section */
    .comments-section {
        max-width: 900px;
        width: 100%;
        margin-top: 40px;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(90deg, #fff, var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 30px;
        text-align: center;
    }

    .comments-count {
        text-align: center;
        color: var(--text-secondary);
        margin-bottom: 30px;
        font-size: 1.1rem;
    }

    /* User Info Badges */
    .user-info-badge {
        background: rgba(0, 123, 255, 0.2);
        border: 1px solid rgba(0, 123, 255, 0.3);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .login-required {
        background: rgba(255, 193, 7, 0.2);
        border: 1px solid rgba(255, 193, 7, 0.3);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        text-align: center;
    }

    .badge-you {
        background: var(--accent);
        color: #000;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.7rem;
        margin-left: 8px;
        font-weight: 600;
    }

    /* Comment Form */
    .comment-form-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 25px;
        padding: 30px;
        box-shadow: var(--glass-shadow);
        margin-bottom: 40px;
    }

    .form-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .comment-form {
        display: grid;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
        font-size: 1rem;
    }

    .form-control {
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        color: var(--text-primary);
        font-size: 1rem;
        transition: var(--transition);
        backdrop-filter: blur(10px);
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--accent);
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 0 0 3px rgba(255, 209, 102, 0.1);
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .btn-submit {
        background: linear-gradient(135deg, #00b09b, #96c93d);
        box-shadow: 0 4px 15px rgba(0, 176, 155, 0.4);
        color: white;
        border: none;
        padding: 14px 30px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 10px;
    }

    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    /* Comments List */
    .comments-list {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    .comment-item {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 25px;
        transition: var(--transition);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .comment-item:hover {
        background: rgba(255, 255, 255, 0.08);
        transform: translateY(-3px);
    }

    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .comment-author {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .author-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f7971e, #ffd200);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: white;
        font-weight: 600;
    }

    .author-info h4 {
        color: var(--text-primary);
        margin-bottom: 4px;
        font-size: 1.1rem;
    }

    .comment-date {
        color: var(--text-secondary);
        font-size: 0.85rem;
    }

    .comment-content {
        color: var(--text-primary);
        line-height: 1.6;
        margin-bottom: 15px;
        font-size: 1rem;
    }

    .comment-actions {
        display: flex;
        gap: 15px;
        margin-top: 15px;
    }

    .btn-reply {
        background: rgba(0, 123, 255, 0.2);
        border: 1px solid rgba(0, 123, 255, 0.3);
        color: #007bff;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        border: none;
    }

    .btn-reply:hover {
        background: rgba(0, 123, 255, 0.3);
        transform: translateY(-2px);
    }

    /* Replies Section */
    .replies-list {
        margin-left: 60px;
        margin-top: 20px;
        border-left: 2px solid rgba(255, 255, 255, 0.1);
        padding-left: 20px;
    }

    .reply-item {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 15px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .reply-item:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .reply-form {
        margin-top: 15px;
        padding: 20px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .reply-form textarea {
        min-height: 80px;
    }

    .no-comments {
        text-align: center;
        padding: 60px 40px;
        color: var(--text-secondary);
    }

    .no-comments i {
        font-size: 4rem;
        margin-bottom: 20px;
        color: var(--text-secondary);
    }

    .no-comments h3 {
        color: var(--text-primary);
        margin-bottom: 15px;
    }

    /* Post Circles */
    .post-circle {
        position: fixed;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        z-index: -1;
        animation: float-home 8s ease-in-out infinite;
    }

    .post-circle.pc1 { width: 120px; height: 120px; top: 10%; left: 5%; animation-delay: 0s; }
    .post-circle.pc2 { width: 80px; height: 80px; top: 75%; left: 90%; animation-delay: 2s; }
    .post-circle.pc3 { width: 60px; height: 60px; top: 85%; left: 15%; animation-delay: 4s; }

    /* Responsive Design */
    @media (max-width: 768px) {
        .post-details-container {
            padding: 20px 15px;
        }
        
        .post-title {
            font-size: 2.2rem;
        }
        
        .post-body {
            padding: 30px 20px;
        }
        
        .post-meta {
            flex-direction: column;
            gap: 15px;
        }
        
        .post-actions {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }
        
        .comment-header {
            flex-direction: column;
            gap: 10px;
        }
        
        .comment-item {
            padding: 20px;
        }
        
        .replies-list {
            margin-left: 20px;
            padding-left: 15px;
        }
        
        .user-info-badge, .login-required {
            flex-direction: column;
            text-align: center;
            gap: 8px;
        }
    }

    @media (max-width: 480px) {
        .post-title {
            font-size: 1.8rem;
        }
        
        .section-title {
            font-size: 1.6rem;
        }
        
        .comment-form-card {
            padding: 20px 15px;
        }
        
        .author-avatar {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .replies-list {
            margin-left: 10px;
            padding-left: 10px;
        }
    }
";

include "inc/header.php";
include "inc/nav.php";
?>

<!-- Post Details Page Content -->
<div class="post-details-container">
    <!-- Floating circles -->
    <div class="post-circle pc1"></div>
    <div class="post-circle pc2"></div>
    <div class="post-circle pc3"></div>

    <div class="post-content">
        <!-- Post Header -->
        <div class="post-header">
            <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <div class="post-meta">
                <div class="meta-item">
                    <i class="fas fa-user"></i>
                    <span>By <?php echo htmlspecialchars($post['username']); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <span><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-eye"></i>
                    <span><?php echo $post['views'] + 1; ?> views</span>
                </div>
                <?php if ($post['category_name']): ?>
                <div class="post-category">
                    <i class="fas fa-tag"></i>
                    <?php echo htmlspecialchars($post['category_name']); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Featured Image -->
        <?php if (!empty($post['image'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="post-image">
        <?php endif; ?>

        <!-- Post Content -->
        <div class="post-body">
            <div class="post-content-text">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>

            <!-- Post Actions -->
            <div class="post-actions">
                <a href="home.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Posts
                </a>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="comments-section">
            <h2 class="section-title">Comments</h2>
            <div class="comments-count">
                <?php 
                $totalComments = count($comments);
                $totalReplies = 0;
                foreach ($comments as $comment) {
                    $totalReplies += count($comment['replies']);
                }
                echo ($totalComments + $totalReplies) . ' ' . (($totalComments + $totalReplies) === 1 ? 'Comment' : 'Comments');
                ?>
            </div>

            <!-- Success/Error Messages -->
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 12px; margin-bottom: 20px;">
                <?php echo $message; ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <!-- Comment Form -->
            <div class="comment-form-card">
                <h3 class="form-title">
                    <i class="fas fa-comment"></i> 
                    Leave a Comment
                </h3>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-info-badge">
                        <i class="fas fa-user-check" style="color: #007bff; font-size: 1.2rem;"></i>
                        <div>
                            <strong style="color: var(--text-primary);">Commenting as:</strong>
                            <span style="color: var(--text-primary);"><?php echo htmlspecialchars($user_name); ?></span>
                        </div>
                    </div>
                    
                    <form method="POST" class="comment-form" id="commentForm">
                        <div class="form-group">
                            <label for="comment" class="form-label">Your Comment *</label>
                            <textarea class="form-control" id="comment" name="comment" placeholder="Share your thoughts..." required><?php echo $_POST['comment'] ?? ''; ?></textarea>
                        </div>
                        
                        <button type="submit" name="add_comment" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> 
                            Post Comment
                        </button>
                    </form>
                <?php else: ?>
                    <div class="login-required">
                        <i class="fas fa-info-circle" style="color: #ffc107; font-size: 1.2rem;"></i>
                        <div>
                            <strong style="color: var(--text-primary);">Login Required</strong>
                            <p style="color: var(--text-primary); margin: 10px 0;">
                                You need to be logged in to post comments and replies.
                            </p>
                            <a href="login.php" class="btn-submit" style="display: inline-flex; text-decoration: none;">
                                <i class="fas fa-sign-in-alt"></i> 
                                Login to Comment
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Comments List -->
            <div class="comments-list">
                <?php if (count($comments) > 0): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-item" id="comment-<?php echo $comment['id']; ?>">
                            <div class="comment-header">
                                <div class="comment-author">
                                    <div class="author-avatar">
                                        <?php echo strtoupper(substr($comment['name'], 0, 1)); ?>
                                    </div>
                                    <div class="author-info">
                                        <h4>
                                            <?php echo htmlspecialchars($comment['name']); ?>
                                            <?php if ($comment['email'] === $user_email): ?>
                                                <span class="badge-you">YOU</span>
                                            <?php endif; ?>
                                        </h4>
                                        <span class="comment-date"><?php echo date('F j, Y g:i A', strtotime($comment['created_at'])); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="comment-content">
                                <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                            </div>
                            
                            <div class="comment-actions">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <button type="button" class="btn-reply" onclick="toggleReplyForm(<?php echo $comment['id']; ?>)">
                                        <i class="fas fa-reply"></i> Reply
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn-reply" onclick="location.href='login.php'">
                                        <i class="fas fa-reply"></i> Login to Reply
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Reply Form (Hidden by default) -->
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="reply-form" id="replyForm<?php echo $comment['id']; ?>" style="display: none;">
                                    <form method="POST">
                                        <input type="hidden" name="parent_id" value="<?php echo $comment['id']; ?>">
                                        <div class="form-group">
                                            <label for="reply<?php echo $comment['id']; ?>" class="form-label">Your Reply</label>
                                            <textarea class="form-control" id="reply<?php echo $comment['id']; ?>" name="reply" placeholder="Write your reply..." required></textarea>
                                        </div>
                                        <div style="display: flex; gap: 10px;">
                                            <button type="submit" name="add_reply" class="btn-submit" style="padding: 10px 20px;">
                                                <i class="fas fa-paper-plane"></i> Post Reply
                                            </button>
                                            <button type="button" class="btn-reply" onclick="toggleReplyForm(<?php echo $comment['id']; ?>)" style="background: rgba(108, 117, 125, 0.2); color: #6c757d;">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Replies List -->
                            <?php if (count($comment['replies']) > 0): ?>
                                <div class="replies-list">
                                    <?php foreach ($comment['replies'] as $reply): ?>
                                        <div class="reply-item" id="reply-<?php echo $reply['id']; ?>">
                                            <div class="comment-header">
                                                <div class="comment-author">
                                                    <div class="author-avatar" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                                        <?php echo strtoupper(substr($reply['name'], 0, 1)); ?>
                                                    </div>
                                                    <div class="author-info">
                                                        <h4 style="font-size: 1rem;">
                                                            <?php echo htmlspecialchars($reply['name']); ?>
                                                            <?php if ($reply['email'] === $user_email): ?>
                                                                <span class="badge-you">YOU</span>
                                                            <?php endif; ?>
                                                        </h4>
                                                        <span class="comment-date" style="font-size: 0.8rem;"><?php echo date('F j, Y g:i A', strtotime($reply['created_at'])); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="comment-content" style="font-size: 0.95rem;">
                                                <?php echo nl2br(htmlspecialchars($reply['comment'])); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-comments">
                        <i class="fas fa-comments"></i>
                        <h3>No Comments Yet</h3>
                        <p>Be the first to share your thoughts!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle reply form visibility
    function toggleReplyForm(commentId) {
        const replyForm = document.getElementById('replyForm' + commentId);
        if (replyForm.style.display === 'none') {
            replyForm.style.display = 'block';
            // Scroll to the reply form
            replyForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            replyForm.style.display = 'none';
        }
    }
    
    // Auto-resize textarea
    document.addEventListener('DOMContentLoaded', function() {
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });
        
        // Add hover effects to comment items
        const commentItems = document.querySelectorAll('.comment-item, .reply-item');
        commentItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>

<?php include "inc/footer.php"; ?>