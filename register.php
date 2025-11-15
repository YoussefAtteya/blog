<?php
include "inc/config.php"
?>
<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}
$errors = [];
$success_message = '';
$username = '';
$email = '';  

 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['name']); 
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = isset($_POST['role']) ? $_POST['role'] : 'user';


     if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "Please fill in all fields.";
    }

     if ($password !== $confirm_password) {
        $errors[] = "The password and confirmation password do not match.";
    }

     if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? ");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "This email  is already taken. Please try another one.";
        }
    }
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        try {
          $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
          $stmt->execute([$username, $email, $hashed_password, $role]);

            $success_message = "Your account has been created successfully! You can now <a href='login.php' class='alert-link'>log in</a>.";
            $username = '';
            $email = '';
            header("Location: login.php");
            exit();

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
  <title>Register | User Portal</title>
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
      width:450px;
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

    .btn-register{
      width:100%;
      padding:12px 0;
      border-radius:50px;
      font-weight:600;
      text-transform:uppercase;
      color:#fff;
      background:linear-gradient(90deg,#f7971e,#ffd200);
      border:none;
      transition:0.3s;
    }

    .btn-register:hover{
      transform:scale(1.05);
      box-shadow:0 0 20px rgba(255,255,255,0.3);
    }

    .link{
      text-align:center;
      margin-top:15px;
      font-size:15px;
    }

    .link a{color:#00c6ff;text-decoration:none;}
    .link a:hover{text-decoration:underline;}

    /* floating circles */
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
    <h2>Create Account</h2>



    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger" role="alert">
        <?php foreach ($errors as $error): ?>
          <p class="mb-0"><?php echo $error; ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    

    <?php if (empty($success_message)): ?>
   



    <form action="" method="post">
      <div class="mb-3">
        <input type="text" name="name" class="form-control" placeholder="Full Name" required>
      </div>
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <div class="mb-3">
        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
      </div>
      <div class="mb-3 text-center" style="color: #fff;">

        <label style="margin-right: 15px;">
          <input type="radio" name="role" value="user" >
          User
        </label>

          <label>
            <input type="radio" name="role" value="admin">
            Admin
          </label>
      </div>

      <button type="submit" class="btn-register">Register</button>
    </form>


    <?php endif; ?>

    <div class="link">
      <p>Already have an account? <a href="login.php">Login</a></p>
      <p><a href="index.php">← Back</a></p>
    </div>
  </div>
</body>
</html>

