<?php
include "inc/config.php";

// Set page title and additional CSS
$pageTitle = "Home | Blog System";
$additionalCSS = "
    /* Home Page Styles */
    .home-container {
        min-height: calc(100vh - 200px);
        padding: 40px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .hero-section {
        text-align: center;
        max-width: 800px;
        margin-bottom: 60px;
    }

    .hero-title {
        font-family: 'Pacifico', cursive;
        font-size: 4rem;
        margin-bottom: 20px;
        background: linear-gradient(90deg, #fff, var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1.2;
    }

    .hero-subtitle {
        font-size: 1.3rem;
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 30px;
    }

    .welcome-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 25px;
        padding: 60px 40px;
        text-align: center;
        box-shadow: var(--glass-shadow);
        max-width: 500px;
        width: 100%;
        margin: 0 auto;
    }

    .typing{
        font-family: 'Pacifico', cursive;
        font-size: 3.5rem;
        display: inline-block;
        white-space: nowrap;
        overflow: hidden;
        vertical-align: middle;
        width: 0;
        border-right: 4px solid rgba(255,255,255,0.95);
        margin-bottom: 10px;
    }

    .typing.animate{
        animation:
            typing 1.4s steps(7,end) forwards,
            blink-caret 0.6s step-end infinite;
    }

    @keyframes typing{
        from { width: 0; }
        to { width: 7ch; } 
    }

    @keyframes blink-caret{
        50% { border-color: transparent; }
    }

    .welcome-text {
        font-size: 1.1rem;
        color: var(--text-secondary);
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .buttons{
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .btn-custom{
        padding: 14px 35px;
        border-radius: 50px;
        font-weight: 600;
        color: #fff;
        border: none;
        transition: transform .3s, box-shadow .3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 1rem;
        min-width: 160px;
        justify-content: center;
    }

    .btn-custom:hover{ 
        transform: translateY(-3px) scale(1.05); 
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
    }

    .btn-login-main{ 
        background: linear-gradient(135deg, #00c6ff, #0072ff);
        box-shadow: 0 8px 25px rgba(0, 114, 255, 0.4);
    }

    .btn-register-main{ 
        background: linear-gradient(135deg, #f7971e, #ffd200);
        box-shadow: 0 8px 25px rgba(247, 151, 30, 0.4);
    }

    .btn-about { 
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        box-shadow: 0 8px 25px rgba(106, 17, 203, 0.4);
    }

    .btn-blog {
        background: linear-gradient(135deg, #00b09b, #96c93d);
        box-shadow: 0 8px 25px rgba(0, 176, 155, 0.4);
    }

    .btn-create-blog {
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
        padding: 15px 40px;
        font-size: 1.1rem;
    }

    .btn-create-blog:hover {
        background: linear-gradient(135deg, #ff5252, #ff3838);
    }

    /* Features Preview */
    .features-preview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        max-width: 1000px;
        width: 100%;
        margin-top: 60px;
    }

    .feature-preview-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 30px 25px;
        text-align: center;
        transition: var(--transition);
        box-shadow: var(--glass-shadow);
    }

    .feature-preview-card:hover {
        transform: translateY(-8px);
        background: rgba(255, 255, 255, 0.15);
    }

    .feature-preview-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #00c6ff, #0072ff);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        box-shadow: 0 6px 20px rgba(0, 114, 255, 0.3);
    }

    .feature-preview-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 12px;
        color: var(--text-primary);
    }

    .feature-preview-desc {
        color: var(--text-secondary);
        line-height: 1.5;
        font-size: 0.95rem;
    }

    /* Blog Section */
    .blog-section {
        max-width: 1200px;
        width: 100%;
        margin-top: 80px;
        text-align: center;
    }

    .section-header {
        margin-bottom: 40px;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(90deg, #fff, var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 15px;
    }

    .section-subtitle {
        color: var(--text-secondary);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Horizontal Scrolling Styles */
    .scrolling-container {
        position: relative;
        max-width: 1200px;
        width: 100%;
        margin: 0 auto 40px;
        overflow: hidden;
        padding: 20px 0;
    }

    .scrolling-track {
        display: flex;
        gap: 25px;
        animation: scroll-horizontal 30s linear infinite;
        padding: 10px 0;
    }

    .scrolling-track:hover {
        animation-play-state: paused;
    }

    @keyframes scroll-horizontal {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(calc(-350px * 5 - 25px * 5));
        }
    }

    /* Single Column Layout for Blog Posts - WIDER (80%) */
    .blog-rows {
        display: flex;
        flex-direction: column;
        gap: 25px;
        max-width: 1200px;
        width: 80%;
        margin: 40px auto;
    }

    /* Normal Blog Cards - Keep original height design */
    .blog-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 30px;
        transition: all 0.3s ease;
        box-shadow: var(--glass-shadow);
        text-align: left;
        position: relative;
        width: 100%;
    }

    .blog-card:hover {
        transform: translateY(-8px) scale(1.02);
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    }

    .blog-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.6s;
    }

    .blog-card:hover::before {
        left: 100%;
    }

    .blog-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .blog-title {
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 10px;
        flex: 1;
    }

    .blog-date {
        color: var(--accent);
        font-size: 0.9rem;
        font-weight: 500;
        white-space: nowrap;
        margin-left: 15px;
    }

    .blog-author {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .author-avatar {
        width: 35px;
        height: 35px;
        background: linear-gradient(135deg, #f7971e, #ffd200);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }

    .author-name {
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .blog-excerpt {
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 20px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .blog-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 20px;
    }

    .blog-tag {
        background: rgba(255, 255, 255, 0.1);
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.8rem;
        color: var(--text-secondary);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .blog-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
    }

    .read-more {
        color: var(--accent);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .read-more:hover {
        color: #fff;
        transform: translateX(5px);
    }

    .blog-stats {
        display: flex;
        gap: 15px;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .stat {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .create-blog-btn {
        margin-top: 30px;
    }

    /* Scroll Dots Navigation */
    .scroll-dots {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin: 30px 0;
    }

    .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .dot:hover {
        background: rgba(255, 255, 255, 0.5);
        transform: scale(1.2);
    }

    .dot.active {
        background: var(--accent);
        transform: scale(1.3);
        box-shadow: 0 0 15px var(--accent);
    }

    /* Gradient fade edges */
    .scrolling-container::before,
    .scrolling-container::after {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 60px;
        z-index: 2;
        pointer-events: none;
    }

    .scrolling-container::before {
        left: 0;
        background: linear-gradient(90deg, var(--primary-gradient) 0%, transparent 100%);
    }

    .scrolling-container::after {
        right: 0;
        background: linear-gradient(270deg, var(--primary-gradient) 0%, transparent 100%);
    }

    /* Stats Bar */
    .stats-bar {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        max-width: 900px;
        width: 100%;
        margin-top: 50px;
    }

    .stat-item {
        text-align: center;
        padding: 20px;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(90deg, #fff, var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 8px;
    }

    .stat-label {
        color: var(--text-secondary);
        font-size: 1rem;
        font-weight: 500;
    }

    /* Additional floating circles for home page */
    .home-circle {
        position: fixed;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        z-index: -1;
        animation: float-home 8s ease-in-out infinite;
    }

    .home-circle.hc1 { width: 120px; height: 120px; top: 15%; left: 8%; animation-delay: 0s; }
    .home-circle.hc2 { width: 80px; height: 80px; top: 70%; left: 85%; animation-delay: 2s; }
    .home-circle.hc3 { width: 60px; height: 60px; top: 85%; left: 20%; animation-delay: 4s; }
    .home-circle.hc4 { width: 100px; height: 100px; top: 25%; right: 10%; animation-delay: 1s; }

    @keyframes float-home {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-25px) rotate(180deg); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 3rem;
        }
        
        .typing {
            font-size: 2.8rem;
        }
        
        .welcome-card {
            padding: 40px 25px;
            margin: 0 15px;
        }
        
        .buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .btn-custom {
            width: 100%;
            max-width: 250px;
        }
        
        .features-preview {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .scrolling-container {
            overflow-x: auto;
            padding: 15px 0;
        }

        .scrolling-track {
            animation: none;
            gap: 20px;
            padding: 10px 15px;
        }

        .blog-rows {
            width: 95%;
        }

        .blog-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .blog-date {
            margin-left: 0;
            margin-top: 5px;
        }
        
        .blog-actions {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .stats-bar {
            grid-template-columns: repeat(2, 1fr);
        }

        @keyframes scroll-horizontal {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(calc(-300px * 5 - 20px * 5));
            }
        }
    }

    @media (max-width: 480px) {
        .home-container {
            padding: 20px 15px;
        }
        
        .hero-title {
            font-size: 2.5rem;
        }
        
        .typing {
            font-size: 2.2rem;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
        }
        
        .section-title {
            font-size: 2rem;
        }
        
        .blog-card {
            padding: 25px 20px;
        }

        .blog-title {
            font-size: 1.2rem;
        }

        .blog-excerpt {
            font-size: 0.9rem;
        }
        
        .stat-number {
            font-size: 2rem;
        }
        
        .stats-bar {
            grid-template-columns: 1fr;
        }

        @keyframes scroll-horizontal {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(calc(-280px * 5 - 20px * 5));
            }
        }
    }
";

// Get latest 5 posts for scrolling section
try {
    $latest_posts_stmt = $pdo->prepare("
        SELECT p.*, u.username, c.name as category_name 
        FROM posts p 
        LEFT JOIN users u ON p.user_id = u.id 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.status = 'published' 
        ORDER BY p.created_at DESC 
        LIMIT 5
    ");
    $latest_posts_stmt->execute();
    $latest_posts = $latest_posts_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $latest_posts = [];
}

// Get all published posts for normal cards section
try {
    $all_posts_stmt = $pdo->prepare("
        SELECT p.*, u.username, c.name as category_name 
        FROM posts p 
        LEFT JOIN users u ON p.user_id = u.id 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.status = 'published' 
        ORDER BY p.created_at DESC
    ");
    $all_posts_stmt->execute();
    $all_posts = $all_posts_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $all_posts = [];
}

include "inc/header.php";
include "inc/nav.php";
?>

<!-- Home Page Content -->
<div class="home-container">
    <!-- Additional floating circles for home page -->
    <div class="home-circle hc1"></div>
    <div class="home-circle hc2"></div>
    <div class="home-circle hc3"></div>
    <div class="home-circle hc4"></div>

    <!-- Hero Section -->
    <div class="hero-section">
        <h1 class="hero-title">Welcome to Blog System</h1>
        <p class="hero-subtitle">Discover amazing content and share your thoughts with the world</p>
    </div>

    <!-- Scrolling Blog Posts Section -->
    <div class="blog-section">
        <div class="section-header">
            <h2 class="section-title">Latest Blog Posts</h2>
            <p class="section-subtitle">Check out our most recent articles</p>
        </div>

        <!-- Create New Blog Button -->
        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="create-blog-btn">
            <a href="create.php" class="btn-custom btn-create-blog">
                <i class="fas fa-plus-circle"></i>
                Create New Blog Post
            </a>
        </div>
        <?php endif; ?>

        <!-- Horizontal Scrolling Container -->
        <div class="scrolling-container">
            <div class="scrolling-track" id="scrollingTrack">
                <?php if (count($latest_posts) > 0): ?>
                    <?php foreach ($latest_posts as $post): 
                        $excerpt = strip_tags($post['content']);
                        if (strlen($excerpt) > 150) {
                            $excerpt = substr($excerpt, 0, 150) . '...';
                        }
                    ?>
                        <div class="blog-card">
                            <div class="blog-header">
                                <h3 class="blog-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                                <span class="blog-date"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                            </div>
                            <div class="blog-author">
                                <div class="author-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span class="author-name"><?php echo htmlspecialchars($post['username']); ?></span>
                            </div>
                            <p class="blog-excerpt">
                                <?php echo $excerpt; ?>
                            </p>
                            <?php if ($post['category_name']): ?>
                            <div class="blog-tags">
                                <span class="blog-tag"><?php echo htmlspecialchars($post['category_name']); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="blog-actions">
                                <a href="details.php?id=<?php echo $post['id']; ?>" class="read-more">
                                    Read More <i class="fas fa-arrow-right"></i>
                                </a>
                                <div class="blog-stats">
                                    <span class="stat">
                                        <i class="fas fa-eye"></i> <?php echo $post['views'] ?? 0; ?>
                                    </span>
                                    <span class="stat">
                                        <i class="fas fa-comment"></i> 
                                        <?php 
                                            // Count comments for this post
                                            try {
                                                $comment_count_stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ? AND status = 'approved'");
                                                $comment_count_stmt->execute([$post['id']]);
                                                echo $comment_count_stmt->fetchColumn();
                                            } catch (PDOException $e) {
                                                echo '0';
                                            }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback content if no posts -->
                    <div class="blog-card">
                        <div class="blog-header">
                            <h3 class="blog-title">Welcome to Our Blog</h3>
                            <span class="blog-date"><?php echo date('M j, Y'); ?></span>
                        </div>
                        <div class="blog-author">
                            <div class="author-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="author-name">Admin</span>
                        </div>
                        <p class="blog-excerpt">
                            This is the beginning of something amazing! Our blog is getting ready to share 
                            incredible content with you. Stay tuned for the first posts coming soon...
                        </p>
                        <div class="blog-tags">
                            <span class="blog-tag">Welcome</span>
                            <span class="blog-tag">Introduction</span>
                        </div>
                        <div class="blog-actions">
                            <a href="#" class="read-more">
                                Read More <i class="fas fa-arrow-right"></i>
                            </a>
                            <div class="blog-stats">
                                <span class="stat">
                                    <i class="fas fa-eye"></i> 0
                                </span>
                                <span class="stat">
                                    <i class="fas fa-comment"></i> 0
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Navigation Dots -->
        <div class="scroll-dots" id="scrollDots">
            <?php for ($i = 0; $i < min(5, count($latest_posts)); $i++): ?>
                <span class="dot <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>"></span>
            <?php endfor; ?>
        </div>
    </div>

    <!-- All Blog Posts Section -->
    <div class="blog-section">
        <div class="section-header">
            <h2 class="section-title">All Blog Posts</h2>
            <p class="section-subtitle">Browse through all our published articles</p>
        </div>

        <div class="blog-rows">
            <?php if (count($all_posts) > 0): ?>
                <?php foreach ($all_posts as $post): 
                    $excerpt = strip_tags($post['content']);
                    if (strlen($excerpt) > 200) {
                        $excerpt = substr($excerpt, 0, 200) . '...';
                    }
                ?>
                    <div class="blog-card">
                        <div class="blog-header">
                            <h3 class="blog-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                            <span class="blog-date"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                        </div>
                        <div class="blog-author">
                            <div class="author-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="author-name"><?php echo htmlspecialchars($post['username']); ?></span>
                        </div>
                        <p class="blog-excerpt">
                            <?php echo $excerpt; ?>
                        </p>
                        <?php if ($post['category_name']): ?>
                        <div class="blog-tags">
                            <span class="blog-tag"><?php echo htmlspecialchars($post['category_name']); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="blog-actions">
                            <a href="details.php?id=<?php echo $post['id']; ?>" class="read-more">
                                Read More <i class="fas fa-arrow-right"></i>
                            </a>
                            <div class="blog-stats">
                                <span class="stat">
                                    <i class="fas fa-eye"></i> <?php echo $post['views'] ?? 0; ?>
                                </span>
                                <span class="stat">
                                    <i class="fas fa-comment"></i> 
                                    <?php 
                                        try {
                                            $comment_count_stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ? AND status = 'approved'");
                                            $comment_count_stmt->execute([$post['id']]);
                                            echo $comment_count_stmt->fetchColumn();
                                        } catch (PDOException $e) {
                                            echo '0';
                                        }
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="blog-card" style="text-align: center; padding: 60px 40px;">
                    <i class="fas fa-file-alt" style="font-size: 4rem; color: var(--text-secondary); margin-bottom: 20px;"></i>
                    <h3 style="color: var(--text-primary); margin-bottom: 15px;">No Posts Yet</h3>
                    <p style="color: var(--text-secondary); margin-bottom: 25px;">
                        There are no published blog posts at the moment. Check back soon for new content!
                    </p>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="create.php" class="btn-custom btn-create-blog" style="display: inline-flex;">
                            <i class="fas fa-plus-circle"></i> Be the First to Post
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const REPEAT_INTERVAL = 10000; 

    // Horizontal scrolling functionality
    document.addEventListener('DOMContentLoaded', function() {
        const scrollingTrack = document.getElementById('scrollingTrack');
        const scrollDots = document.getElementById('scrollDots');
        const cards = document.querySelectorAll('.blog-card');
        
        // Clone cards for infinite scroll effect
        const cloneCards = () => {
            const firstCards = Array.from(cards).slice(0, 5);
            firstCards.forEach(card => {
                const clone = card.cloneNode(true);
                scrollingTrack.appendChild(clone);
            });
        };

        // Initialize infinite scroll
        cloneCards();

        // Auto-scroll functionality
        let scrollPosition = 0;
        const scrollSpeed = 1; // pixels per frame
        let animationId;

        const autoScroll = () => {
            if (scrollingTrack) {
                scrollPosition += scrollSpeed;
                
                // Reset position when scrolled enough
                if (scrollPosition >= cards[0].offsetWidth * 5 + 25 * 5) {
                    scrollPosition = 0;
                }
                
                scrollingTrack.style.transform = `translateX(-${scrollPosition}px)`;
                animationId = requestAnimationFrame(autoScroll);
            }
        };

        // Start auto-scroll
        autoScroll();

        // Pause on hover
        if (scrollingTrack) {
            scrollingTrack.addEventListener('mouseenter', () => {
                cancelAnimationFrame(animationId);
            });

            scrollingTrack.addEventListener('mouseleave', () => {
                animationId = requestAnimationFrame(autoScroll);
            });
        }

        // Dot navigation
        if (scrollDots) {
            scrollDots.addEventListener('click', (e) => {
                if (e.target.classList.contains('dot')) {
                    const index = parseInt(e.target.getAttribute('data-index'));
                    
                    // Update active dot
                    document.querySelectorAll('.dot').forEach(dot => {
                        dot.classList.remove('active');
                    });
                    e.target.classList.add('active');
                    
                    // Scroll to position
                    if (scrollingTrack) {
                        const scrollTo = index * (cards[0].offsetWidth + 25);
                        scrollingTrack.style.transform = `translateX(-${scrollTo}px)`;
                        scrollPosition = scrollTo;
                    }
                }
            });
        }

        // Touch/swipe support for mobile
        let startX = 0;
        let currentX = 0;
        let isDragging = false;

        if (scrollingTrack) {
            scrollingTrack.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                isDragging = true;
                cancelAnimationFrame(animationId);
            });

            scrollingTrack.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                currentX = e.touches[0].clientX;
                const diff = startX - currentX;
                scrollingTrack.style.transform = `translateX(-${scrollPosition + diff}px)`;
            });

            scrollingTrack.addEventListener('touchend', () => {
                if (!isDragging) return;
                isDragging = false;
                scrollPosition += (startX - currentX);
                animationId = requestAnimationFrame(autoScroll);
            });
        }

        // Add hover effects to blog cards
        const blogCards = document.querySelectorAll('.blog-card');
        
        blogCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>

<?php include "inc/footer.php"; ?>