<?php
session_start();
require_once __DIR__ . "/../admin/db.php";
if (isset($_POST['login'])) {
    $input = $_POST['email']; // username or email
    $pass  = $_POST['password'];

    $q = mysqli_query($conn,
        "SELECT * FROM users WHERE email='$input' OR username='$input'"
    );
    $user = mysqli_fetch_assoc($q);

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['profile_img'] = $user['profile_img'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid login details";
    }
}
?>

<form method="POST" class="auth-form">
    <h2>Login</h2>

    <?php if(isset($error)) echo "<p>$error</p>"; ?>

    <input type="text" name="email" placeholder="Email or Username" required>
    <input type="password" name="password" placeholder="Password" required>

    <button name="login">Login</button>
    <p>No account? <a href="register.php">Register</a></p>
</form>
