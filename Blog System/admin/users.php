<?php
include "inc/config.php";
session_start();
requireAdmin();

// Handle user actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    // Prevent admin from deleting themselves
    if ($user_id === $_SESSION['user_id'] && $action === 'delete') {
        header("Location: users.php?error=self_delete");
        exit();
    }
    
    if ($action === 'make_admin') {
        $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
        $stmt->execute([$user_id]);
    } elseif ($action === 'make_user') {
        $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ?");
        $stmt->execute([$user_id]);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
    }
    
    header("Location: users.php");
    exit();
}

// Get all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Manage Users";
$additionalCSS = "
<style>
.admin-main-content {
    margin-left: 320px;
    margin-right: 20px;
    padding: 30px;
    min-height: calc(100vh - 160px);
    transition: var(--transition);
}

.users-container {
    max-width: 1400px;
    margin: 0 auto;
}

.users-title {
    font-family: 'Pacifico', cursive;
    font-size: 3rem;
    margin-bottom: 40px;
    background: linear-gradient(90deg, #fff, var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-align: center;
}

.users-table {
    background: var(--glass-bg);
    backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--glass-shadow);
}

.table-header {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr 2fr;
    gap: 15px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.1);
    border-bottom: 1px solid var(--glass-border);
    font-weight: 600;
    color: var(--text-primary);
}

.user-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr 2fr;
    gap: 15px;
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: var(--transition);
}

.user-row:hover {
    background: rgba(255, 255, 255, 0.05);
}

.user-row:last-child {
    border-bottom: none;
}

.user-name, .user-email, .user-date {
    color: var(--text-primary);
}

.user-role {
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
    text-align: center;
}

.role-admin {
    background: rgba(0, 123, 255, 0.2);
    color: #007bff;
    border: 1px solid rgba(0, 123, 255, 0.3);
}

.role-user {
    background: rgba(40, 167, 69, 0.2);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.user-actions {
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

.btn-admin {
    background: rgba(0, 123, 255, 0.2);
    color: #007bff;
    border: 1px solid rgba(0, 123, 255, 0.3);
}

.btn-user {
    background: rgba(40, 167, 69, 0.2);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
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

.current-user {
    background: rgba(255, 209, 102, 0.1);
    border-left: 3px solid var(--accent);
}

.no-users {
    text-align: center;
    padding: 60px 40px;
    color: var(--text-secondary);
}

.no-users i {
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

.admin-circle.uc1 { width: 120px; height: 120px; top: 15%; right: 8%; animation-delay: 0s; }
.admin-circle.uc2 { width: 80px; height: 80px; bottom: 20%; left: 60%; animation-delay: 2s; }

@media (max-width: 1200px) {
    .admin-main-content {
        margin-left: 20px;
        margin-right: 20px;
    }
}

@media (max-width: 768px) {
    .users-title {
        font-size: 2.5rem;
    }
    
    .table-header {
        display: none;
    }
    
    .user-row {
        grid-template-columns: 1fr;
        gap: 10px;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.05);
    }
    
    .user-actions {
        justify-content: center;
        margin-top: 10px;
    }
}
</style>
";

include "../inc/header.php";
// Include your existing navigation (unchanged)
include "inc/nav_admin.php";
// FIX: Include the correct sidebar path
include "inc/sidebar.php";
?>

<div class="admin-main-content">
    <div class="users-container">
        <!-- Floating circles -->
        <div class="admin-circle uc1"></div>
        <div class="admin-circle uc2"></div>

        <h1 class="users-title">Manage Users</h1>
        
        <?php if (isset($_GET['error']) && $_GET['error'] === 'self_delete'): ?>
        <div class="alert alert-danger" style="background: rgba(220, 53, 69, 0.2); border: 1px solid rgba(220, 53, 69, 0.3); color: #dc3545; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
            You cannot delete your own account!
        </div>
        <?php endif; ?>
        
        <div class="users-table">
            <div class="table-header">
                <div>Username</div>
                <div>Email</div>
                <div>Role</div>
                <div>Posts</div>
                <div>Joined</div>
                <div>Actions</div>
            </div>
            
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $user): 
                    // Count user's posts

                    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
                    $stmt2->execute([$user['id']]);
                    $post_count = $stmt2->fetchColumn();
                ?>
                <div class="user-row <?php echo $user['id'] === $_SESSION['user_id'] ? 'current-user' : ''; ?>">
                    <div class="user-name">
                        <?php echo htmlspecialchars($user['username']); ?>
                        <?php if ($user['id'] === $_SESSION['user_id']): ?>
                            <span style="color: var(--accent); font-size: 0.8rem;">(You)</span>
                        <?php endif; ?>
                    </div>
                    <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                    <div>
                        <span class="user-role role-<?php echo $user['role']; ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </div>
                    <div class="user-posts"><?php echo $post_count; ?> posts</div>
                    <div class="user-date"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></div>
                    <div class="user-actions">


                        <?php if ($user['role'] === 'user'): ?>
                            <a href="users.php?action=make_admin&id=<?php echo $user['id']; ?>" class="action-btn btn-admin">Make Admin</a>
                        
                            <?php elseif ($user['role'] === 'admin'): ?>
                            <a href="users.php?action=make_user&id=<?php echo $user['id']; ?>" class="action-btn btn-user">Make User</a>
                        <?php endif; ?>
                        
                        <?php if ($user['role'] === 'admin' ): ?>
                            <a href="users.php?action=delete&id=<?php echo $user['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete user <?php echo htmlspecialchars($user['username']); ?>?')">Delete</a>
                        <?php endif; ?>

                        
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-users">
                    <i class="fas fa-users"></i>
                    <h3>No Users Found</h3>
                    <p>There are no users in the system yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../inc/footer.php"; ?>