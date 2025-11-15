<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
$user_id = $_SESSION['user_id'];

// Get current user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
    
    // Set current values
    $username = $user['username'];
    $email = $user['email'];
    $current_role = $user['role'];
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Debug information
    error_log("=== EDIT PROFILE FORM SUBMISSION ===");
    error_log("User ID: " . $user_id);
    error_log("Username: " . $username);
    error_log("Email: " . $email);
    error_log("Current password provided: " . (!empty($current_password) ? 'Yes' : 'No'));
    error_log("New password provided: " . (!empty($new_password) ? 'Yes' : 'No'));

    // Validation
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username is required!";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters long!";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address!";
    }
    
    // Check if username already exists (excluding current user)
    try {
        $check_username = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $check_username->execute([$username, $user_id]);
        if ($check_username->fetch()) {
            $errors[] = "Username already exists!";
        }
    } catch (PDOException $e) {
        $errors[] = "Error checking username availability.";
        error_log("Username check error: " . $e->getMessage());
    }
    
    // Check if email already exists (excluding current user)
    try {
        $check_email = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check_email->execute([$email, $user_id]);
        if ($check_email->fetch()) {
            $errors[] = "Email already exists!";
        }
    } catch (PDOException $e) {
        $errors[] = "Error checking email availability.";
        error_log("Email check error: " . $e->getMessage());
    }
    
    // Password change logic
    $password_changed = false;
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        error_log("Password change fields detected");
        
        if (empty($current_password)) {
            $errors[] = "Current password is required to change password!";
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect!";
            error_log("Current password verification failed");
        } elseif (empty($new_password)) {
            $errors[] = "New password is required!";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters long!";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match!";
        } else {
            $password_changed = true;
            error_log("Password change validated successfully");
        }
    }

    // If no errors, update database
    if (empty($errors)) {
        try {
            if ($password_changed) {
                // Update with new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
                
                error_log("Executing UPDATE with password change");
                $result = $stmt->execute([$username, $email, $hashed_password, $user_id]);
                $rowCount = $stmt->rowCount();
                
                error_log("Update result: " . ($result ? 'true' : 'false'));
                error_log("Rows affected: " . $rowCount);
                
                if ($rowCount > 0) {
                    $message = "Profile updated successfully! Password has been changed.";
                    $message_type = "success";
                    error_log("SUCCESS: Profile updated with password change");
                } else {
                    $message = "No changes were made to your profile.";
                    $message_type = "info";
                    error_log("INFO: No rows affected during update");
                }
            } else {
                // Update without changing password - only update if data actually changed
                if ($username !== $user['username'] || $email !== $user['email']) {
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                    
                    error_log("Executing UPDATE without password change");
                    $result = $stmt->execute([$username, $email, $user_id]);
                    $rowCount = $stmt->rowCount();
                    
                    error_log("Update result: " . ($result ? 'true' : 'false'));
                    error_log("Rows affected: " . $rowCount);
                    
                    if ($rowCount > 0) {
                        $message = "Profile updated successfully!";
                        $message_type = "success";
                        error_log("SUCCESS: Profile updated without password change");
                    } else {
                        $message = "No changes were made to your profile.";
                        $message_type = "info";
                        error_log("INFO: No rows affected during update");
                    }
                } else {
                    $message = "No changes were made to your profile.";
                    $message_type = "info";
                    error_log("INFO: No changes detected in form data");
                }
            }
            
            // Update session username if changed
            if ($username !== $_SESSION['username']) {
                $_SESSION['username'] = $username;
                error_log("Session username updated to: " . $username);
            }
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                error_log("User data refreshed - Username: " . $user['username'] . ", Email: " . $user['email']);
                // Update current values for form
                $username = $user['username'];
                $email = $user['email'];
            } else {
                error_log("WARNING: Failed to refresh user data after update");
            }
            
        } catch (PDOException $e) {
            $message = "Error updating profile: " . $e->getMessage();
            $message_type = "danger";
            error_log("DATABASE ERROR: " . $e->getMessage());
        }
    } else {
        $message = implode("<br>", $errors);
        $message_type = "danger";
        error_log("VALIDATION ERRORS: " . implode(" | ", $errors));
    }
}

// Test function to verify database connection and permissions
function testDatabaseUpdate($pdo, $user_id) {
    try {
        $test_username = "test_user_" . rand(1000, 9999);
        $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
        $result = $stmt->execute([$test_username, $user_id]);
        $rowCount = $stmt->rowCount();
        
        error_log("TEST UPDATE - Result: " . ($result ? 'true' : 'false') . ", Rows: " . $rowCount);
        return $rowCount > 0;
    } catch (PDOException $e) {
        error_log("TEST UPDATE ERROR: " . $e->getMessage());
        return false;
    }
}

// Set page title and additional CSS
$pageTitle = "Edit Profile | Blog System";
$additionalCSS = "
    /* Edit Profile Styles */
    .edit-profile-container {
        min-height: calc(100vh - 200px);
        padding: 40px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
    }

    .edit-profile-header {
        text-align: center;
        max-width: 800px;
        margin-bottom: 40px;
    }

    .edit-profile-title {
        font-family: 'Pacifico', cursive;
        font-size: 3rem;
        margin-bottom: 10px;
        background: linear-gradient(90deg, #fff, var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .edit-profile-subtitle {
        font-size: 1.2rem;
        color: var(--text-secondary);
    }

    .edit-profile-form-container {
        max-width: 700px;
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

    .form-control {
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

    .form-control:focus {
        outline: none;
        border-color: var(--accent);
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 0 0 3px rgba(255, 209, 102, 0.1);
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .password-toggle {
        position: relative;
    }

    .password-toggle-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        cursor: pointer;
        transition: var(--transition);
    }

    .password-toggle-icon:hover {
        color: var(--accent);
    }

    .form-section {
        margin-bottom: 35px;
        padding-bottom: 25px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: var(--accent);
    }

    .form-hint {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-hint i {
        color: var(--accent);
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .btn-save, .btn-cancel, .btn-test {
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

    .btn-save {
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

    .btn-test {
        background: linear-gradient(135deg, #6f42c1, #e83e8c);
        box-shadow: 0 4px 15px rgba(111, 66, 193, 0.4);
        color: white;
    }

    .btn-save:hover, .btn-cancel:hover, .btn-test:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .current-info {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .info-label {
        color: var(--text-secondary);
        font-weight: 500;
    }

    .info-value {
        color: var(--text-primary);
        font-weight: 600;
    }

    .edit-profile-circles {
        position: fixed;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        z-index: -1;
        animation: float-home 8s ease-in-out infinite;
    }

    .edit-profile-circles.epc1 { width: 120px; height: 120px; top: 10%; left: 5%; animation-delay: 0s; }
    .edit-profile-circles.epc2 { width: 80px; height: 80px; top: 75%; left: 90%; animation-delay: 2s; }
    .edit-profile-circles.epc3 { width: 60px; height: 60px; top: 85%; left: 15%; animation-delay: 4s; }

    /* Debug Info Styles */
    .debug-info {
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 15px;
        margin-top: 20px;
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .debug-info h4 {
        color: var(--accent);
        margin-bottom: 10px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .edit-profile-container {
            padding: 20px 15px;
        }
        
        .edit-profile-title {
            font-size: 2.5rem;
        }
        
        .form-card {
            padding: 30px 20px;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-save, .btn-cancel, .btn-test {
            width: 100%;
            justify-content: center;
        }
        
        .info-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
    }

    @media (max-width: 480px) {
        .edit-profile-title {
            font-size: 2rem;
        }
        
        .form-title {
            font-size: 1.5rem;
        }
        
        .section-title {
            font-size: 1.1rem;
        }
    }
";

include "inc/header.php";
include "inc/nav.php";
?>

<!-- Edit Profile Page Content -->
<div class="edit-profile-container">
    <!-- Floating circles -->
    <div class="edit-profile-circles epc1"></div>
    <div class="edit-profile-circles epc2"></div>
    <div class="edit-profile-circles epc3"></div>

    <!-- Page Header -->
    <div class="edit-profile-header">
        <h1 class="edit-profile-title">Edit Profile</h1>
        <p class="edit-profile-subtitle">Update your personal information and preferences</p>
    </div>

    <!-- Edit Profile Form Container -->
    <div class="edit-profile-form-container">
        <!-- Success/Error Messages -->
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 12px; margin-bottom: 20px;">
            <?php echo $message; ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>


        <!-- Edit Profile Form -->
        <div class="form-card">
            <div class="form-header">
                <h2 class="form-title">
                    <i class="fas fa-user-edit"></i> Update Your Profile
                </h2>
                <p class="form-subtitle">Make changes to your account information</p>
            </div>

            <form method="POST" id="editProfileForm">
                <!-- Current Information Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i> Current Information
                    </h3>
                    <div class="current-info">
                        <div class="info-item">
                            <span class="info-label">Current Role:</span>
                            <span class="info-value"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Member Since:</span>
                            <span class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Current Username:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Current Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Basic Information Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-user"></i> Basic Information
                    </h3>
                    
                    <div class="form-group">
                        <label for="username" class="form-label">
                            <i class="fas fa-user"></i> Username *
                        </label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Enter your username" required>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i> Username must be at least 3 characters long
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email Address *
                        </label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter your email address" required>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i> We'll never share your email with anyone else
                        </div>
                    </div>
                </div>

                <!-- Password Change Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-lock"></i> Change Password
                    </h3>
                    <p class="form-hint" style="margin-bottom: 20px;">
                        <i class="fas fa-info-circle"></i> Leave password fields blank if you don't want to change your password
                    </p>

                    <div class="form-group">
                        <label for="current_password" class="form-label">
                            <i class="fas fa-key"></i> Current Password
                        </label>
                        <div class="password-toggle">
                            <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter your current password">
                            <i class="fas fa-eye password-toggle-icon" data-target="current_password"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="new_password" class="form-label">
                            <i class="fas fa-lock"></i> New Password
                        </label>
                        <div class="password-toggle">
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password">
                            <i class="fas fa-eye password-toggle-icon" data-target="new_password"></i>
                        </div>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i> Password must be at least 6 characters long
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">
                            <i class="fas fa-lock"></i> Confirm New Password
                        </label>
                        <div class="password-toggle">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                            <i class="fas fa-eye password-toggle-icon" data-target="confirm_password"></i>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                
                    
                    <a href="profile.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Security Tips Card -->
        <div class="form-card">
            <div class="form-header">
                <h2 class="form-title">
                    <i class="fas fa-shield-alt"></i> Security Tips
                </h2>
            </div>
            <div class="security-tips">
                <div class="form-hint" style="margin-bottom: 15px;">
                    <i class="fas fa-check-circle" style="color: #28a745;"></i>
                    Use a strong, unique password
                </div>
                <div class="form-hint" style="margin-bottom: 15px;">
                    <i class="fas fa-check-circle" style="color: #28a745;"></i>
                    Never share your password with anyone
                </div>
                <div class="form-hint" style="margin-bottom: 15px;">
                    <i class="fas fa-check-circle" style="color: #28a745;"></i>
                    Update your password regularly
                </div>
                <div class="form-hint">
                    <i class="fas fa-check-circle" style="color: #28a745;"></i>
                    Use different passwords for different sites
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Edit profile page loaded successfully');
        
        // Password toggle functionality
        const toggleIcons = document.querySelectorAll('.password-toggle-icon');
        
        toggleIcons.forEach(icon => {
            icon.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
                console.log('Toggled password visibility for: ' + targetId);
            });
        });

        // Form validation
        const form = document.getElementById('editProfileForm');
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');

        form.addEventListener('submit', function(e) {
            console.log('Form submission started');
            
            let valid = true;
            
            // Check if new password is provided but confirmation is empty
            if (newPassword.value && !confirmPassword.value) {
                console.log('Password confirmation missing');
                alert('Please confirm your new password');
                confirmPassword.focus();
                valid = false;
            }
            
            // Check if passwords match
            if (newPassword.value && confirmPassword.value && newPassword.value !== confirmPassword.value) {
                console.log('Passwords do not match');
                alert('New passwords do not match!');
                confirmPassword.focus();
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
                console.log('Form submission prevented due to validation errors');
            } else {
                console.log('Form validation passed, proceeding with submission');
            }
        });

        // Real-time password match indicator
        function checkPasswordMatch() {
            if (newPassword.value && confirmPassword.value) {
                if (newPassword.value === confirmPassword.value) {
                    confirmPassword.style.borderColor = '#28a745';
                    console.log('Passwords match');
                } else {
                    confirmPassword.style.borderColor = '#dc3545';
                    console.log('Passwords do not match');
                }
            } else {
                confirmPassword.style.borderColor = 'var(--glass-border)';
            }
        }

        newPassword.addEventListener('input', checkPasswordMatch);
        confirmPassword.addEventListener('input', checkPasswordMatch);

        // Add hover effects to form elements
        const formControls = document.querySelectorAll('.form-control');
        formControls.forEach(control => {
            control.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            control.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });

        // Character count for username
        const usernameInput = document.getElementById('username');
        usernameInput.addEventListener('input', function() {
            if (this.value.length < 3 && this.value.length > 0) {
                this.style.borderColor = '#dc3545';
                console.log('Username too short');
            } else if (this.value.length >= 3) {
                this.style.borderColor = '#28a745';
                console.log('Username length OK');
            } else {
                this.style.borderColor = 'var(--glass-border)';
            }
        });
    });

    // Test database update function
    function testDatabaseUpdate() {
        console.log('Testing database update...');
        fetch('test_update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'test=true'
        })
        .then(response => response.text())
        .then(data => {
            console.log('Test result:', data);
            alert('Test completed: ' + data);
        })
        .catch(error => {
            console.error('Test error:', error);
            alert('Test failed: ' + error);
        });
    }
</script>

<?php include "inc/footer.php"; ?>