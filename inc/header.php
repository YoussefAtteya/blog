<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Block System'; ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-gradient: radial-gradient(circle at top left, #6a11cb, #2575fc);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.8);
            --accent: #ffd166;
            --transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--primary-gradient);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Background Elements */
        .background-circles .circle {
            position: fixed;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            z-index: -1;
            animation: float 6s ease-in-out infinite;
        }

        .background-circles .circle.c1 { width: 100px; height: 100px; top: 12%; left: 10%; }
        .background-circles .circle.c2 { width: 150px; height: 150px; top: 62%; left: 70%; animation-delay: 1s; }
        .background-circles .circle.c3 { width: 80px; height: 80px; top: 78%; left: 25%; animation-delay: 0.6s; }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-22px); }
        }

        /* Navigation Styles */
        .nav-container {
            width: 95%;
            max-width: 1400px;
            margin: 20px auto;
            position: relative;
        }

        .navbar-main {
            width: 100%;
            padding: 18px 35px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 25px;
            box-shadow: var(--glass-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideDown 0.8s ease;
            position: relative;
            overflow: hidden;
            z-index: 1000;
        }

        .navbar-main::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.6s;
        }

        .navbar-main:hover::before {
            left: 100%;
        }

        @keyframes slideDown {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 114, 255, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .logo-icon::before {
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

        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .logo-text {
            display: flex;
            flex-direction: column;
        }

        .logo-main {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(90deg, #fff, var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            letter-spacing: 0.5px;
        }

        .nav-links {
            display: flex;
            gap: 8px;
            list-style: none;
            flex: 2;
            justify-content: center;
            margin: 0;
        }

        .nav-item {
            position: relative;
            padding: 12px 24px;
            border-radius: 50px;
            transition: var(--transition);
            cursor: pointer;
            font-weight: 500;
            color: var(--text-secondary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            overflow: hidden;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }

        .nav-item:hover::before {
            left: 100%;
        }

        .nav-item:hover, .nav-item.active {
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .nav-item.active::after {
            content: '';
            position: absolute;
            bottom: 8px;
            left: 50%;
            transform: translateX(-50%);
            width: 6px;
            height: 6px;
            background: var(--accent);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--accent);
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
            justify-content: flex-end;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f7971e, #ffd200);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(247, 151, 30, 0.4);
        }

        .user-avatar:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .auth-btn {
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            border: none;
            color: white;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Poppins', sans-serif;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn-login {
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            box-shadow: 0 4px 15px rgba(0, 114, 255, 0.4);
        }

        .btn-logout {
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
            box-shadow: 0 4px 15px rgba(255, 75, 43, 0.4);
        }

        .auth-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        /* Mobile Menu Styles */
        .mobile-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-top: none;
            border-radius: 0 0 25px 25px;
            padding: 20px;
            box-shadow: var(--glass-shadow);
            z-index: 999;
        }

        .mobile-menu.active {
            display: block;
            animation: slideDownMobile 0.3s ease-out;
        }

        @keyframes slideDownMobile {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .mobile-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 12px;
            transition: var(--transition);
            margin-bottom: 8px;
        }

        .mobile-nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .mobile-logout {
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
            margin-top: 15px;
            justify-content: center;
            font-weight: 600;
        }

        .nav-toggle {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 21px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            z-index: 1001;
        }

        .nav-toggle span {
            display: block;
            height: 3px;
            width: 100%;
            background: var(--text-primary);
            border-radius: 3px;
            transition: var(--transition);
            transform-origin: center;
        }

        .nav-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }

        .nav-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .nav-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .user-section, .nav-links {
                display: none;
            }
            .nav-toggle {
                display: flex;
            }
        }

        @media (max-width: 480px) {
            .navbar-main {
                padding: 12px 15px;
            }
            .logo-main {
                font-size: 1.5rem;
            }
            .logo-icon {
                width: 40px;
                height: 40px;
            }
        }

        /* Common Components */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: var(--glass-shadow);
            padding: 40px;
        }

        .btn-custom {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            color: #fff;
            border: none;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        /* Footer Styles */
        .simple-footer {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-top: 1px solid var(--glass-border);
            padding: 25px 20px;
            margin-top: 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
        }

        .simple-footer p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin: 0;
            font-weight: 400;
        }

        .heart {
            color: #ff6b6b;
            animation: heartbeat 2s ease-in-out infinite;
            display: inline-block;
        }

        @keyframes heartbeat {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.2);
            }
        }

        .simple-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.05), transparent);
            transition: left 0.6s;
        }

        .simple-footer:hover::before {
            left: 100%;
        }

        /* Home Page Specific Styles */
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

        .typing {
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

        .typing.animate {
            animation:
                typing 1.4s steps(7,end) forwards,
                blink-caret 0.6s step-end infinite;
        }

        @keyframes typing {
            from { width: 0; }
            to { width: 7ch; } 
        }

        @keyframes blink-caret {
            50% { border-color: transparent; }
        }

        .welcome-text {
            font-size: 1.1rem;
            color: var(--text-secondary);
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .btn-login-main { 
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            box-shadow: 0 8px 25px rgba(0, 114, 255, 0.4);
        }

        .btn-register-main { 
            background: linear-gradient(135deg, #f7971e, #ffd200);
            box-shadow: 0 8px 25px rgba(247, 151, 30, 0.4);
        }

        .btn-about { 
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            box-shadow: 0 8px 25px rgba(106, 17, 203, 0.4);
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
                transform: translateX(calc(-350px * 3 - 25px * 3));
            }
        }

        .blog-card {
            flex: 0 0 350px;
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            transition: all 0.3s ease;
            box-shadow: var(--glass-shadow);
            text-align: left;
            position: relative;
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

        /* Responsive Design for Home Page */
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

            .blog-card {
                flex: 0 0 300px;
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
                    transform: translateX(calc(-300px * 3 - 20px * 3));
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
                flex: 0 0 280px;
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
                    transform: translateX(calc(-280px * 3 - 20px * 3));
                }
            }
        }

        /* Page Specific Styles Can Be Added Below */
        <?php if (isset($additionalCSS)) echo $additionalCSS; ?>
    </style>
        
    <script>
        // Mobile Navigation Toggle - Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            const navToggle = document.getElementById('navToggle');
            const mobileMenu = document.getElementById('mobileMenu');
            const navContainer = document.querySelector('.nav-container');

            console.log('Navigation elements:', { navToggle, mobileMenu, navContainer });

            if (navToggle && mobileMenu && navContainer) {
                navToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    console.log('Toggle clicked');
                    mobileMenu.classList.toggle('active');
                    navToggle.classList.toggle('active');
                });

                // Close mobile menu when clicking on a menu item
                const mobileNavItems = document.querySelectorAll('.mobile-nav-item');
                mobileNavItems.forEach(item => {
                    item.addEventListener('click', () => {
                        mobileMenu.classList.remove('active');
                        navToggle.classList.remove('active');
                    });
                });

                // Close menu when clicking outside
                document.addEventListener('click', (e) => {
                    if (!navContainer.contains(e.target)) {
                        mobileMenu.classList.remove('active');
                        navToggle.classList.remove('active');
                    }
                });

                // Close menu on escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        mobileMenu.classList.remove('active');
                        navToggle.classList.remove('active');
                    }
                });
            } else {
                console.error('Navigation elements not found!');
            }
        });
    </script>   
</head>
<body>
    <!-- Background Elements -->
    <div class="background-circles">
        <div class="circle c1"></div>
        <div class="circle c2"></div>
        <div class="circle c3"></div>
    </div>
    