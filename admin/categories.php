<?php
include "inc/config.php";
session_start();
requireAdmin();

// Initialize variables
$message = "";
$message_type = "";

// Handle add category
if (isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $badge_color = $_POST['badge_color'] ?? 'primary';
    
    if (empty($name)) {
        $message = "Category name is required!";
        $message_type = "danger";
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name, description, badge_color) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $badge_color]);
        
        $message = "Category added successfully!";
        $message_type = "success";
    }
}

// Handle update category
if (isset($_POST['update_category'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $edit_id = $_GET['edit_id'];
    
    if (empty($name)) {
        $message = "Category name is required!";
        $message_type = "danger";
    } else {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $description, $edit_id]);
        
        $message = "Category updated successfully!";
        $message_type = "success";
        echo "<script>window.location.href = 'categories.php';</script>";
        exit();
    }
}

// Handle delete category
if (isset($_GET['del_id'])) {
    $del_id = $_GET['del_id'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$del_id]);
    
    $message = "Category deleted successfully!";
    $message_type = "success";
    echo "<script>window.location.href = 'categories.php';</script>";
    exit();
}

// Fetch all categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY created_at DESC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get category for editing
$edit_category = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $edit_category = $stmt->fetch(PDO::FETCH_ASSOC);
}

$pageTitle = "Manage Categories";
$additionalCSS = "
<style>
.admin-main-content {
    margin-left: 320px;
    margin-right: 20px;
    padding: 30px;
    min-height: calc(100vh - 160px);
    transition: var(--transition);
}

.categories-container {
    max-width: 1400px;
    margin: 0 auto;
}

.categories-title {
    font-family: 'Pacifico', cursive;
    font-size: 3rem;
    margin-bottom: 40px;
    background: linear-gradient(90deg, #fff, var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-align: center;
}

.category-form {
    background: var(--glass-bg);
    backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: var(--glass-shadow);
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr auto;
    gap: 15px;
    align-items: end;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    color: var(--text-primary);
    font-weight: 500;
    margin-bottom: 8px;
}

.form-input, .form-select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    padding: 12px 15px;
    color: var(--text-primary);
    transition: var(--transition);
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: var(--accent);
    background: rgba(255, 255, 255, 0.15);
}

.btn-primary {
    background: linear-gradient(135deg, #00c6ff, #0072ff);
    border: none;
    border-radius: 12px;
    padding: 12px 25px;
    color: white;
    font-weight: 600;
    transition: var(--transition);
    cursor: pointer;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 114, 255, 0.4);
}

.categories-table {
    background: var(--glass-bg);
    backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--glass-shadow);
}

.table-header {
    display: grid;
    grid-template-columns: 80px 1fr 2fr 120px 200px;
    gap: 15px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.1);
    border-bottom: 1px solid var(--glass-border);
    font-weight: 600;
    color: var(--text-primary);
}

.category-row {
    display: grid;
    grid-template-columns: 80px 1fr 2fr 120px 200px;
    gap: 15px;
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: var(--transition);
}

.category-row:hover {
    background: rgba(255, 255, 255, 0.05);
}

.category-row:last-child {
    border-bottom: none;
}

.category-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.badge-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.badge-primary { background: rgba(0, 114, 255, 0.2); color: #0072ff; border: 1px solid rgba(0, 114, 255, 0.3); }
.badge-success { background: rgba(40, 167, 69, 0.2); color: #28a745; border: 1px solid rgba(40, 167, 69, 0.3); }
.badge-warning { background: rgba(255, 193, 7, 0.2); color: #ffc107; border: 1px solid rgba(255, 193, 7, 0.3); }
.badge-danger { background: rgba(220, 53, 69, 0.2); color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.3); }
.badge-info { background: rgba(23, 162, 184, 0.2); color: #17a2b8; border: 1px solid rgba(23, 162, 184, 0.3); }
.badge-secondary { background: rgba(108, 117, 125, 0.2); color: #6c757d; border: 1px solid rgba(108, 117, 125, 0.3); }
.badge-dark { background: rgba(52, 58, 64, 0.2); color: #343a40; border: 1px solid rgba(52, 58, 64, 0.3); }

.dot-primary { background: #0072ff; }
.dot-success { background: #28a745; }
.dot-warning { background: #ffc107; }
.dot-danger { background: #dc3545; }
.dot-info { background: #17a2b8; }
.dot-secondary { background: #6c757d; }
.dot-dark { background: #343a40; }

.category-actions {
    display: flex;
    gap: 8px;
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

.btn-edit {
    background: rgba(0, 114, 255, 0.2);
    color: #0072ff;
    border: 1px solid rgba(0, 114, 255, 0.3);
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

.no-categories {
    text-align: center;
    padding: 60px 40px;
    color: var(--text-secondary);
}

.no-categories i {
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

.admin-circle.cat1 { width: 120px; height: 120px; top: 15%; right: 8%; animation-delay: 0s; }
.admin-circle.cat2 { width: 80px; height: 80px; bottom: 20%; left: 60%; animation-delay: 2s; }

@media (max-width: 1200px) {
    .admin-main-content {
        margin-left: 20px;
        margin-right: 20px;
    }
}

@media (max-width: 768px) {
    .categories-title {
        font-size: 2.5rem;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .table-header {
        display: none;
    }
    
    .category-row {
        grid-template-columns: 1fr;
        gap: 10px;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.05);
    }
    
    .category-actions {
        justify-content: center;
        margin-top: 10px;
    }
}
</style>
";

include "../inc/header.php";
include "inc/nav_admin.php";
include "inc/sidebar.php";
?>

<div class="admin-main-content">
    <div class="categories-container">
        <!-- Floating circles -->
        <div class="admin-circle cat1"></div>
        <div class="admin-circle cat2"></div>

        <h1 class="categories-title">Manage Categories</h1>
        
        <!-- Add/Edit Category Form -->
        <div class="category-form">
            <h3><?php echo isset($edit_category) ? 'Edit Category' : 'Add New Category'; ?></h3>
            <form method="POST" class="form-grid">
                <div class="form-group">
                    <label class="form-label">Category Name *</label>
                    <input type="text" class="form-input" name="name" 
                           value="<?php echo isset($edit_category) ? htmlspecialchars($edit_category['name']) : ''; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" class="form-input" name="description"
                           value="<?php echo isset($edit_category) ? htmlspecialchars($edit_category['description']) : ''; ?>">
                </div>
              
                
                <div class="form-group">
                    <button type="submit" class="btn-primary" name="<?php echo isset($edit_category) ? 'update_category' : 'add_category'; ?>">
                        <?php echo isset($edit_category) ? 'Update Category' : 'Add Category'; ?>
                    </button>
                    <?php if (isset($edit_category)): ?>
                        <a href="categories.php" class="action-btn btn-secondary" style="margin-top: 8px; display: block; text-align: center;">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Categories Table -->
        <div class="categories-table">
            <div class="table-header">
                <div>ID</div>
                <div>Name</div>
                <div>Description</div>
                <div>Actions</div>
            </div>
            
            <?php if (count($categories) > 0): ?>
                <?php foreach ($categories as $category): ?>
                <div class="category-row">
                    <div><?php echo $category['id']; ?></div>
                    <div><?php echo htmlspecialchars($category['name']); ?></div>
                    <div><?php echo htmlspecialchars($category['description']); ?></div>
                   
                    <div class="category-actions">
                        <a href="categories.php?edit_id=<?php echo $category['id']; ?>" class="action-btn btn-edit">Edit</a>
                        <a href="categories.php?del_id=<?php echo $category['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-categories">
                    <i class="fas fa-tags"></i>
                    <h3>No Categories Found</h3>
                    <p>Start by adding your first category above.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../inc/footer.php"; ?>