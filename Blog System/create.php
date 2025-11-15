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

// Initialize variables
$message = "";
$message_type = "";
$title = "";
$content = "";
$category_id = "";
$status = "pending"; // Changed to "pending" as default
$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Get categories for dropdown
try {
    $categories_stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = $_POST['category_id'];
    $status = $_POST['status'] ?? 'pending'; // Changed to 'pending' as default

    // Validation
    if (empty($title)) {
        $message = "Post title is required!";
        $message_type = "danger";
    } elseif (empty($content)) {
        $message = "Post content is required!";
        $message_type = "danger";
    } elseif (empty($category_id)) {
        $message = "Please select a category!";
        $message_type = "danger";
    } else {
        try {
            // Handle image upload
            $image = "";
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = "uploads/";
                
                // Create uploads directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file_name = time() . '_' . uniqid() . '.' . $file_extension;
                $file_path = $upload_dir . $file_name;
                
                // Check file type
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array(strtolower($file_extension), $allowed_types)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                        $image = $file_name;
                    } else {
                        $message = "Failed to upload image!";
                        $message_type = "danger";
                    }
                } else {
                    $message = "Only JPG, JPEG, PNG, GIF, and WebP files are allowed!";
                    $message_type = "danger";
                }
            }

            // If no errors, save to database
            if (empty($message)) {
                // Check if the user_id exists in users table
                $user_check = $pdo->prepare("SELECT id FROM users WHERE id = ?");
                $user_check->execute([$user_id]);
                $user_exists = $user_check->fetch();
                
                if (!$user_exists) {
                    $message = "Error: User does not exist!";
                    $message_type = "danger";
                } else {
                    // Insert post with user_id and status as 'pending'
                    $stmt = $pdo->prepare("INSERT INTO posts (title, content, image, category_id, user_id, status) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $content, $image, $category_id, $user_id, $status]);

                    $message = "Post created successfully and is pending approval!";
                    $message_type = "success";
                    
                    // Clear form fields
                    $title = "";
                    $content = "";
                    $category_id = "";
                    $status = "pending";
                    
                    // Redirect to home page after 2 seconds
                    echo "<script>setTimeout(function() { window.location.href = 'home.php'; }, 2000);</script>";
                }
            }
            
        } catch (PDOException $e) {
            $message = "Error creating post: " . $e->getMessage();
            $message_type = "danger";
        }
    }
}

// Set page title and additional CSS
$pageTitle = "Create New Post | Blog System";
$additionalCSS = "
    /* Create Post Styles */
    .create-container {
        min-height: calc(100vh - 200px);
        padding: 40px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
    }

    .create-header {
        text-align: center;
        max-width: 800px;
        margin-bottom: 40px;
    }

    .create-title {
        font-family: 'Pacifico', cursive;
        font-size: 3rem;
        margin-bottom: 10px;
        background: linear-gradient(90deg, #fff, var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .create-subtitle {
        font-size: 1.2rem;
        color: var(--text-secondary);
    }

    .create-form-container {
        max-width: 900px;
        width: 100%;
        margin: 0 auto;
    }

    .form-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 25px;
        padding: 40px;
        box-shadow: var(--glass-shadow);
        margin-bottom: 30px;
    }

    .form-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .form-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .form-subtitle {
        color: var(--text-secondary);
        font-size: 1rem;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
        font-size: 1rem;
    }

    .form-control, .form-select, .form-textarea {
        width: 100%;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        color: var(--text-primary);
        font-size: 1rem;
        transition: var(--transition);
        backdrop-filter: blur(10px);
    }

    .form-textarea {
        min-height: 200px;
        resize: vertical;
        font-family: inherit;
    }

    .form-control:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: var(--accent);
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 0 0 3px rgba(255, 209, 102, 0.1);
    }

    .form-control::placeholder, .form-textarea::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .file-upload {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .file-upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        border: 2px dashed var(--glass-border);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.05);
        cursor: pointer;
        transition: var(--transition);
        text-align: center;
    }

    .file-upload-label:hover {
        border-color: var(--accent);
        background: rgba(255, 255, 255, 0.1);
    }

    .file-upload-icon {
        font-size: 2.5rem;
        color: var(--accent);
        margin-bottom: 10px;
    }

    .file-upload-text {
        color: var(--text-primary);
        font-weight: 500;
        margin-bottom: 5px;
    }

    .file-upload-hint {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .file-input {
        position: absolute;
        left: -9999px;
        opacity: 0;
    }

    .file-preview {
        margin-top: 15px;
        text-align: center;
    }

    .preview-image {
        max-width: 200px;
        max-height: 150px;
        border-radius: 8px;
        border: 2px solid var(--glass-border);
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .btn-create, .btn-draft, .btn-cancel {
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        border: none;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 1rem;
        cursor: pointer;
    }

    .btn-create {
        background: linear-gradient(135deg, #00b09b, #96c93d);
        box-shadow: 0 4px 15px rgba(0, 176, 155, 0.4);
        color: white;
    }

    .btn-cancel {
        background: linear-gradient(135deg, #ff416c, #ff4b2b);
        box-shadow: 0 4px 15px rgba(255, 75, 43, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-create:hover, .btn-cancel:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .character-count {
        text-align: right;
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-top: 5px;
    }

    .create-circles {
        position: fixed;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        z-index: -1;
        animation: float-home 8s ease-in-out infinite;
    }

    .create-circles.cc1 { width: 120px; height: 120px; top: 10%; left: 5%; animation-delay: 0s; }
    .create-circles.cc2 { width: 80px; height: 80px; top: 75%; left: 90%; animation-delay: 2s; }
    .create-circles.cc3 { width: 60px; height: 60px; top: 85%; left: 15%; animation-delay: 4s; }

    /* Pending Status Notice */
    .pending-notice {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.3);
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
        text-align: center;
    }

    .pending-notice i {
        color: #ffc107;
        font-size: 1.2rem;
        margin-right: 8px;
    }

    .pending-notice p {
        margin: 0;
        color: var(--text-primary);
        font-weight: 500;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .create-container {
            padding: 20px 15px;
        }
        
        .create-title {
            font-size: 2.5rem;
        }
        
        .form-card {
            padding: 30px 20px;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-create, .btn-cancel {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .create-title {
            font-size: 2rem;
        }
        
        .form-title {
            font-size: 1.5rem;
        }
    }
";

include "inc/header.php";
include "inc/nav.php";
?>

<!-- Create Post Page Content -->
<div class="create-container">
    <!-- Floating circles -->
    <div class="create-circles cc1"></div>
    <div class="create-circles cc2"></div>
    <div class="create-circles cc3"></div>

    <!-- Page Header -->
    <div class="create-header">
        <h1 class="create-title">Create New Post</h1>
        <p class="create-subtitle">Share your thoughts and ideas with the world</p>
    </div>

    <!-- Create Form Container -->
    <div class="create-form-container">
        <!-- Success/Error Messages -->
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 12px; margin-bottom: 20px;">
            <?php echo $message; ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Pending Approval Notice -->
        <div class="pending-notice">
            <i class="fas fa-clock"></i>
            <p>All posts require admin approval before being published.</p>
        </div>

        <!-- Create Post Form -->
        <div class="form-card">
            <div class="form-header">
                <h2 class="form-title">
                    <i class="fas fa-edit"></i> Write Your Blog Post
                </h2>
                <p class="form-subtitle">Fill in the details below to create your post</p>
            </div>

            <form method="POST" enctype="multipart/form-data" id="createPostForm">
                <!-- Post Title -->
                <div class="form-group">
                    <label for="title" class="form-label">
                        <i class="fas fa-heading"></i> Post Title *
                    </label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" placeholder="Enter a catchy title for your post..." required>
                </div>

                <!-- Post Content -->
                <div class="form-group">
                    <label for="content" class="form-label">
                        <i class="fas fa-file-alt"></i> Content *
                    </label>
                    <textarea class="form-control form-textarea" id="content" name="content" placeholder="Write your post content here..." required><?php echo htmlspecialchars($content); ?></textarea>
                    <div class="character-count">
                        <span id="charCount">0</span> characters
                    </div>
                </div>

                <!-- Category Selection -->
                <div class="form-group">
                    <label for="category_id" class="form-label">
                        <i class="fas fa-tag"></i> Category *
                    </label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Select a category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status Selection (Hidden - always set to pending) -->
                <input type="hidden" name="status" id="status" value="pending">

                <!-- Featured Image -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-image"></i> Featured Image
                    </label>
                    <div class="file-upload">
                        <input type="file" class="file-input" id="image" name="image" accept="image/*">
                        <label for="image" class="file-upload-label" id="fileUploadLabel">
                            <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                            <span class="file-upload-text">Choose Featured Image</span>
                            <span class="file-upload-hint">JPG, PNG, GIF, WebP - Max 5MB</span>
                        </label>
                    </div>
                    <div class="file-preview" id="filePreview" style="display: none;">
                        <img src="" alt="Preview" class="preview-image" id="previewImage">
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="home.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn-create">
                        <i class="fas fa-paper-plane"></i> Submit for Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Character count for content
        const contentTextarea = document.getElementById('content');
        const charCount = document.getElementById('charCount');
        
        contentTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
        
        // Trigger initial count
        charCount.textContent = contentTextarea.value.length;

        // File upload preview
        const fileInput = document.getElementById('image');
        const filePreview = document.getElementById('filePreview');
        const previewImage = document.getElementById('previewImage');
        const fileUploadLabel = document.getElementById('fileUploadLabel');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    filePreview.style.display = 'block';
                    fileUploadLabel.innerHTML = `
                        <i class="fas fa-check-circle file-upload-icon" style="color: #28a745;"></i>
                        <span class="file-upload-text">Image Selected</span>
                        <span class="file-upload-hint">${file.name}</span>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                filePreview.style.display = 'none';
                fileUploadLabel.innerHTML = `
                    <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                    <span class="file-upload-text">Choose Featured Image</span>
                    <span class="file-upload-hint">JPG, PNG, GIF, WebP - Max 5MB</span>
                `;
            }
        });

        // Add hover effects to form elements
        const formControls = document.querySelectorAll('.form-control, .form-select, .form-textarea');
        formControls.forEach(control => {
            control.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            control.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });
    });
</script>

<?php include "inc/footer.php"; ?>