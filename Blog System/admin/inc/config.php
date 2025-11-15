<?php
// Admin config - include main config
require_once '../inc/config.php';

// Admin-specific configurations can go here

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin';
}

// Redirect if not admin
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: ../login.php");
        exit();
    }
}

// Function to get current page for sidebar active state
function getCurrentPage() {
    return basename($_SERVER['PHP_SELF']);
}

// Function to get pending counts for badges
function getPendingCounts() {
    global $pdo;
    $pending_posts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'pending'")->fetchColumn();
    $pending_comments = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();
    return [
        'posts' => $pending_posts,
        'comments' => $pending_comments
    ];
}
?>