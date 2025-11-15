<?php
include "inc/config.php";
?>
  
<?php

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: home.php"); 
    exit();
}

$errors = [];
$email = '';    

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Please enter both email and password.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    
                    header("Location: home.php"); 
                    exit();
                } else {
                    $errors[] = "Incorrect email or password.";
                }
            } else {
                $errors[] = "Incorrect email or password.";
            }

        } catch (PDOException $e) {
            $errors[] = "An internal server error occurred. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | User Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{
      height:100vh;
      background: radial-gradient(circle at top left, #6a11cb, #2575fc);
      display:flex;
      align-items:center;
      justify-content:center;
      font-family:'Poppins',sans-serif;
      color:#fff;
      overflow:hidden;
    }

    .glass-card{
      background:rgba(255,255,255,0.1);
      backdrop-filter:blur(12px);
      border-radius:25px;
      padding:60px 80px;
      box-shadow:0 8px 30px rgba(0,0,0,0.3);
      animation:fadeIn 1s ease-in-out;
      width:420px;
    }

    @keyframes fadeIn{
      from{opacity:0;transform:translateY(40px)}
      to{opacity:1;transform:translateY(0)}
    }

    h2{
      font-weight:600;
      text-align:center;
      margin-bottom:25px;
    }

    .form-control{
      background:rgba(255,255,255,0.1);
      border:none;
      color:#fff;
      border-radius:12px;
      padding:12px;
    }

    .form-control::placeholder{color:#ccc;}
    .form-control:focus{
      background:rgba(255,255,255,0.2);
      outline:none;
      box-shadow:none;
    }

    .btn-login{
      width:100%;
      padding:12px 0;
      border-radius:50px;
      font-weight:600;
      text-transform:uppercase;
      color:#fff;
      background:linear-gradient(90deg,#00c6ff,#0072ff);
      border:none;
      transition:0.3s;
    }

    .btn-login:hover{
      transform:scale(1.05);
      box-shadow:0 0 20px rgba(255,255,255,0.3);
    }

    .link{
      text-align:center;
      margin-top:15px;
      font-size:15px;
    }

    .link a{color:#ffd166;text-decoration:none;}
    .link a:hover{text-decoration:underline;}

    .circle{
      position:absolute;
      border-radius:50%;
      background:rgba(255,255,255,0.15);
      animation:float 6s ease-in-out infinite;
      z-index:0;
    }

    @keyframes float{
      0%,100%{transform:translateY(0)}
      50%{transform:translateY(-25px)}
    }

    .circle:nth-child(1){width:100px;height:100px;top:15%;left:10%}
    .circle:nth-child(2){width:160px;height:160px;top:65%;left:70%;animation-delay:2s}
    .circle:nth-child(3){width:90px;height:90px;top:80%;left:25%;animation-delay:1s}
  </style>
</head>
<body>
 
  <div class="circle"></div>
  <div class="circle"></div>
  <div class="circle"></div>

  <div class="glass-card">
    <h2>Login</h2>



     <?php if (!empty($errors)): ?>
      <div class="alert alert-danger" role="alert">
        <?php foreach ($errors as $error): ?>
          <p class="mb-0"><?php echo $error; ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>


    
    <form action="" method="post">
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <button type="submit" class="btn-login">Sign In</button>
    </form>
    <div class="link">
      <p>Don't have an account? <a href="register.php">Register</a></p>
      <p><a href="index.php">← Back</a></p>
    </div>
  </div>
</body>
</html>
