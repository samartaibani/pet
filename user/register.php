<?php
session_start();
require_once __DIR__ . "/../admin/db.php";
if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $pass     = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Image upload
    $img = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    if ($img) {
        move_uploaded_file($tmp, "uploads/$img");
    } else {
        $img = "default.png";
    }

    $check = mysqli_query($conn,
        "SELECT * FROM users WHERE email='$email' OR username='$username'"
    );

    if (mysqli_num_rows($check) > 0) {
        $error = "Email or Username already exists";
    } else {
        mysqli_query($conn,
            "INSERT INTO users (username, email, password, profile_img)
             VALUES ('$username','$email','$pass','$img')"
        );
        header("Location: login.php");
        exit;
    }
}
?>

<form method="POST" enctype="multipart/form-data" class="auth-form">
    <h2>Register</h2>

    <?php if(isset($error)) echo "<p>$error</p>"; ?>

    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="file" name="image">

    <button name="register">Create Account</button>
    <p>Already have an account? <a href="login.php">Login</a></p>
</form>
