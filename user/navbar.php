<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<link rel="stylesheet" href="css/navbar.css">

<nav class="navbar">
    <div class="logo">
        🐾 PetShop
    </div>

    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>

        <?php if (isset($_SESSION['user_id'])) { ?>
            <li class="profile-box">
                <span><?php echo $_SESSION['username']; ?></span>
                <a href="profile.php"><img src="uploads/<?php echo $_SESSION['profile_img']; ?>" alt="Profile"></a>
            </li>
        <?php } else { ?>
            <li><a href="login.php" class="btn">Login</a></li>
        <?php } ?>
    </ul>
</nav>
