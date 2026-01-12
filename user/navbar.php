<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../admin/db.php";

/* WISHLIST COUNT */
$wishlist_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $w = mysqli_fetch_assoc(mysqli_query(
        $conn, "SELECT COUNT(*) AS total FROM wishlist WHERE user_id=$uid"
    ));
    $wishlist_count = (int)($w['total'] ?? 0);
}

/* CART COUNT */
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $c = mysqli_fetch_assoc(mysqli_query(
        $conn, "SELECT SUM(quantity) AS total FROM cart WHERE user_id=$uid"
    ));
    $cart_count = (int)($c['total'] ?? 0);
}
?>

<link rel="stylesheet" href="css/navbar.css?v=<?= time() ?>">

<header class="site-header">

    <!-- ===== TOP INFO BAR ===== -->
    <div class="top-info">
        <div class="container info-flex">
            <div class="logo">🐾 PetShop</div>

            <div class="info-item">🕒 Mon–Fri : 8:00 – 9:00</div>
            <div class="info-item">📞 +91 99999 99999</div>
            <div class="info-item">✉ info@petshop.com</div>
        </div>
    </div>

    <!-- ===== NAVBAR ===== -->
    <nav class="main-nav">
        <div class="container nav-flex">

            <!-- DESKTOP MENU -->
            <ul class="nav-links" id="navMenu">
                <li><a href="index.php">Home</a></li>
                <li><a href="pets.php">Pets</a></li>

                <li class="wishlist-nav">
                    <a href="wishlist.php">❤️
                        <?php if ($wishlist_count > 0) { ?>
                            <span class="count"><?= $wishlist_count ?></span>
                        <?php } ?>
                    </a>
                </li>

                <li class="cart-nav">
                    <a href="cart.php">🛒
                        <?php if ($cart_count > 0) { ?>
                            <span class="count"><?= $cart_count ?></span>
                        <?php } ?>
                    </a>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="profile-box">
                        <span class="username">
                            <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
                        </span>

                        <img src="/pet/user/uploads/<?= $_SESSION['profile_img'] ?? 'default.png' ?>" class="profile-img"onclick="toggleProfileMenu()">
                        <div class="profile-dropdown" id="profileDropdown">
                            <a href="profile.php">👤 Profile</a>
                            <a href="change_password.php">🔑 Password</a>
                            <a href="my_orders.php">📦 Orders</a>
                            <a href="my_coupons.php">🎟 Coupons</a>
                            <a href="logout.php" class="logout-link">🚪 Logout</a>
                        </div>
                    </li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn">Login</a></li>
                <?php endif; ?>
            </ul>

            <!-- MOBILE BUTTON -->
            <button class="menu-toggle" onclick="toggleMenu()">☰ Menu</button>
        </div>
    </nav>

</header>

<script src="js/navbar.js"></script>
