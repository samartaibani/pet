<?php
session_start();
require_once __DIR__ . "/../admin/db.php";

/* ================= FETCH ACTIVE PETS ================= */
$pets = mysqli_query($conn, "
    SELECT * FROM pets 
    WHERE status = 1
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Pets</title>
    <link rel="stylesheet" href="css/pets.css?v=<?php echo time(); ?>">
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="content">

<h2 class="page-title">Available Pets</h2>

<div class="pet-grid">

<?php
if (mysqli_num_rows($pets) > 0) {
    while ($p = mysqli_fetch_assoc($pets)) {

        $image = (!empty($p['image']) && file_exists(__DIR__ . "/../admin/uploads/" . $p['image']))
            ? $p['image']
            : "default.png";
?>
    <div class="pet-box">

        <!-- IMAGE + WISHLIST ICON -->
        <div class="img-box">
            <img src="../admin/uploads/<?= htmlspecialchars($image) ?>" alt="Pet Image">

            <!-- ❤️ Flipkart style wishlist -->
            <a href="wishlist_add.php?id=<?= $p['id'] ?>" class="wishlist-icon">
                ❤
            </a>
        </div>

        <h3><?= htmlspecialchars($p['pet_name']) ?></h3>

        <p><strong>Age:</strong> <?= htmlspecialchars($p['age']) ?></p>
        <p><strong>Size:</strong> <?= htmlspecialchars($p['pet_size']) ?></p>
        <p><strong>Gender:</strong> <?= htmlspecialchars($p['gender']) ?></p>

        <p class="price">₹<?= htmlspecialchars($p['price']) ?></p>

        <?php if ($p['quantity'] > 0) { ?>
            <a href="cart_add.php?id=<?= $p['id'] ?>" class="btn">
                Add to Cart
            </a>
        <?php } else { ?>
            <span class="out">Out of Stock</span>
        <?php } ?>

    </div>
<?php
    }
} else {
    echo "<p>No pets available</p>";
}
?>

</div>
</div>

</body>
</html>
