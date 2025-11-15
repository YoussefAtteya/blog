<?php
include "inc/config.php";
include "inc/header.php";
?>


  <style>
    /* Main Content Styles */
    .main-content {
        height: calc(100vh - 120px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .glass-card{
      position:relative;
      background:rgba(255,255,255,0.1);
      backdrop-filter:blur(12px);
      border-radius:20px;
      padding:70px 90px;
      text-align:center;
      box-shadow:0 8px 30px rgba(0,0,0,0.25);
    }

    .typing{
      font-family:'Pacifico',cursive;
      font-size:64px;
      display:inline-block;
      white-space:nowrap;
      overflow:hidden;
      vertical-align:middle;
      width:0;
      border-right:4px solid rgba(255,255,255,0.95);
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

    .buttons{
      margin-top:28px;
    }

    .btn-custom{
      width:200px;padding:12px 0;border-radius:999px;font-weight:600;margin:8px;color:#fff;border:none;
      transition:transform .22s,box-shadow .22s;
    }

    .btn-custom:hover{ 
        transform:scale(1.06); 
        box-shadow:0 10px 30px rgba(0,0,0,0.25);
    }

    .btn-login-main{ 
        background:linear-gradient(90deg,#00c6ff,#0072ff);
    }

    .btn-register{ 
        background:linear-gradient(90deg,#f7971e,#ffd200);
    }

    .circle{ 
        position:absolute;
        border-radius:50%;
        background:rgba(255,255,255,0.12); 
        z-index:0; 
        animation:float 6s ease-in-out infinite;
    }

    @keyframes float{
        0%,100%{transform:translateY(0)}
        50%{transform:translateY(-22px)}
    }

  </style>
</head>
<body>
 
  <!-- Include Navigation -->
  <?php include "inc/nav.php"; ?>

  <!-- Main Content -->
    <div class="main-content">
      <div class="glass-card">
          <span id="welcome" class="typing">Welcome</span>
          <div class="buttons">
              <a href="login.php" class="btn btn-custom btn-login-main">Login</a>
              <a href="register.php" class="btn btn-custom btn-register">Register</a>
          </div>
    </div>

    

  </div>

  <script>
    const REPEAT_INTERVAL = 10000; 
    const welcome = document.getElementById('welcome');

    function triggerTyping() {
        welcome.classList.remove('animate');
        void welcome.offsetWidth;
        welcome.classList.add('animate');
    }

    window.addEventListener('load', () => {
        setTimeout(triggerTyping, 50);
        setInterval(() => {
            welcome.classList.remove('animate');
            setTimeout(triggerTyping, 250); 
        }, REPEAT_INTERVAL);
    });
  </script>
    <?php
include "inc/footer.php"
?>


