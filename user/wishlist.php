<?php
session_start();
require_once __DIR__ . "/../admin/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

$items = mysqli_query($conn, "
    SELECT pets.*
    FROM wishlist
    JOIN pets ON pets.id = wishlist.pet_id
    WHERE wishlist.user_id = $user_id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Wishlist</title>
    <link rel="stylesheet" href="css/wishlist.css?v=<?= time() ?>">
</head>
<body>

<?php include 'navbar.php'; ?>

<h2 class="page-title">My Wishlist</h2>

<div class="pet-grid">

<?php if (mysqli_num_rows($items) > 0) {
    while ($p = mysqli_fetch_assoc($items)) {

        $image = (!empty($p['image']) && file_exists(__DIR__ . "/../admin/uploads/" . $p['image']))
            ? $p['image']
            : "default.png";
?>
    <div class="pet-box">

        <img src="../admin/uploads/<?= htmlspecialchars($image) ?>">

        <h3><?= htmlspecialchars($p['pet_name']) ?></h3>

        <p class="price">₹<?= htmlspecialchars($p['price']) ?></p>

        <a href="cart_add.php?id=<?= $p['id'] ?>" class="btn">
            Add to Cart
        </a>

        <a href="wishlist_remove.php?id=<?= $p['id'] ?>" class="out">
            Remove
        </a>

    </div>
<?php } } else { ?>
    <p>No items in wishlist</p>
<?php } ?>

</div>

</body>
</html>
