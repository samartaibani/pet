<?php
session_start();
require_once __DIR__ . "/../admin/db.php";

/* ===== REGISTER ===== */
if(isset($_POST['register'])){
    $username = mysqli_real_escape_string($conn,$_POST['username']);
    $email    = mysqli_real_escape_string($conn,$_POST['email']);
    $pass     = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $img = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    if($img){
        move_uploaded_file($tmp,"uploads/$img");
    }else{
        $img = "default.png";
    }

    $check = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($check)>0){
        $error = "Email already exists";
    }else{
        mysqli_query($conn,
            "INSERT INTO users(username,email,password,profile_img)
             VALUES('$username','$email','$pass','$img')"
        );
        $success = "Account created! Please login.";
    }
}

/* ===== LOGIN ===== */
if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $pass  = $_POST['password'];

    $q = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");
    $u = mysqli_fetch_assoc($q);

    if($u && password_verify($pass,$u['password'])){
        $_SESSION['user_id'] = $u['id'];
        header("Location: index.php");
        exit;
    }else{
        $login_error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pet Shop | Login & Register</title>
    <link rel="stylesheet" href="css/login.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="container" id="container">

    <!-- REGISTER -->
    <div class="form-container sign-up">
        <form method="POST" enctype="multipart/form-data">
            <h1>Create Account</h1><br>

            <?php if(isset($error)) echo "<p class='err'>$error</p>"; ?>
            <?php if(isset($success)) echo "<p class='ok'>$success</p>"; ?>

            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="file" name="image">

            <button name="register">Sign Up</button>
        </form>
    </div>

    <!-- LOGIN -->
    <div class="form-container sign-in">
        <form method="POST">
            <h1>Login</h1>

            <?php if(isset($login_error)) echo "<p class='err'>$login_error</p>"; ?>

            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <button name="login">Sign In</button>
        </form>
    </div>

    <!-- TOGGLE -->
    <div class="toggle-container">
        <div class="toggle">
            <div class="toggle-panel toggle-left">
                <h1>Welcome Back 🐾</h1>
                <p>Login to manage pets & orders</p>
                <button class="hidden" id="loginBtn">Sign In</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Hello, Pet Lover!</h1>
                <p>Register to adopt & care</p>
                <button class="hidden" id="registerBtn">Sign Up</button>
            </div>
        </div>
    </div>

</div>

<script src="js/login.js"></script>
</body>
</html>
