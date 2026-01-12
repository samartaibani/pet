<?php
session_start();
require_once __DIR__ . "/../admin/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

/* ================= FETCH CART ITEMS ================= */
$items = mysqli_query($conn, "
    SELECT 
        pets.*, 
        pets.quantity AS stock_qty, 
        cart.quantity AS cart_qty
    FROM cart
    JOIN pets ON pets.id = cart.pet_id
    WHERE cart.user_id = $user_id
");

/* ================= CALCULATE TOTAL ================= */
$total = 0;
$item_count = 0;
$cart_items = [];

if ($items && mysqli_num_rows($items) > 0) {
    while ($row = mysqli_fetch_assoc($items)) {
        $row['subtotal'] = $row['price'] * $row['cart_qty'];
        $total += $row['subtotal'];
        $item_count += $row['cart_qty'];
        $cart_items[] = $row;
    }
}

/* ================= DELIVERY ================= */
$delivery_charge = ($total < 1499) ? 40 : 0;

/* ================= GST ================= */
$gst_rate = 12;
$gst_amount = round(($total * $gst_rate) / 100, 2);

/* ================= COUPON ================= */
$discount = $_SESSION['discount'] ?? 0;
$coupon_error = "";

if (isset($_POST['apply_coupon'])) {

    $code = trim($_POST['coupon_code']);

    $coupon = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM coupons WHERE code='$code' AND status=1")
    );

    if (!$coupon) {
        $coupon_error = "Invalid coupon code";
        $discount = 0;
        unset($_SESSION['discount']);
    }
    elseif ($total < $coupon['min_amount']) {
        $coupon_error = "Minimum order ₹{$coupon['min_amount']} required";
        $discount = 0;
        unset($_SESSION['discount']);
    }
    else {
        $discount = ($coupon['type'] === 'flat')
            ? $coupon['value']
            : round(($total * $coupon['value']) / 100);

        $_SESSION['discount'] = $discount;
    }
}

/* ================= FINAL TOTAL ================= */
$grand_total = max(0, $total + $delivery_charge + $gst_amount - $discount);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/cart.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include 'navbar.php'; ?>

<h2 class="page-title">Shopping Cart</h2>

<?php if ($item_count > 0) { ?>

<div class="cart-layout">

    <!-- LEFT : CART ITEMS -->
    <div class="pet-grid">
        <?php foreach ($cart_items as $p) {

            $image = (!empty($p['image']) && file_exists(__DIR__ . "/../admin/uploads/" . $p['image']))
                ? $p['image']
                : "default.png";
        ?>
        <div class="pet-box">

            <div class="img-box">
                <img src="../admin/uploads/<?= htmlspecialchars($image) ?>" alt="">
            </div>

            <div>
                <h3><?= htmlspecialchars($p['pet_name']) ?></h3>
                <p>₹<?= $p['price'] ?></p>

                <div class="qty-box">
                    <a href="cart_update.php?id=<?= $p['id'] ?>&action=minus" class="qty-btn">−</a>
                    <span class="qty"><?= $p['cart_qty'] ?></span>

                    <?php if ($p['cart_qty'] < $p['stock_qty']) { ?>
                        <a href="cart_update.php?id=<?= $p['id'] ?>&action=plus" class="qty-btn">+</a>
                    <?php } else { ?>
                        <span class="qty-btn disabled">+</span>
                    <?php } ?>
                </div>
            </div>

            <div class="price">₹<?= $p['subtotal'] ?></div>

            <a href="cart_remove.php?id=<?= $p['id'] ?>" class="out">✕</a>

        </div>
        <?php } ?>
    </div>

    <!-- RIGHT : SUMMARY -->
    <div class="cart-summary">

        <h3>Order Summary</h3>

        <p>
            <span>Subtotal (<?= $item_count ?> items)</span>
            <strong>₹<?= $total ?></strong>
        </p>

        <p>
            <span>Delivery</span>
            <strong><?= $delivery_charge ? "₹40" : "FREE" ?></strong>
        </p>

        <?php if ($delivery_charge == 0) { ?>
            <div class="free-badge">🎁 FREE Delivery Applied</div>
        <?php } ?>

        <p>
            <span>GST (<?= $gst_rate ?>%)</span>
            <strong>₹<?= $gst_amount ?></strong>
        </p>

        <!-- COUPON -->
        <form method="post" class="coupon-box">

            <input type="text" name="coupon_code" placeholder="Enter coupon code" required>
            <button type="submit" name="apply_coupon">Apply</button>

            <?php if ($coupon_error) { ?>
                <div class="coupon-message error">
                    <span class="icon">✖</span>
                    <span><?= $coupon_error ?></span>
                </div>
            <?php } ?>

            <?php if ($discount > 0) { ?>
                <div class="coupon-message success">
                    <span class="icon">✔</span>
                    <span>Coupon Applied Successfully</span>
                </div>
            <?php } ?>

        </form>

        <hr>

        <p class="total-line">
            <span>Total Amount</span>
            <strong>₹<?= $grand_total ?></strong>
        </p>

        <a href="checkout.php" class="place-order-btn">PLACE ORDER</a>

    </div>

</div>

<?php } else { ?>

<p class="empty-cart">Your cart is empty</p>

<?php } ?>

<!-- JS -->
<script src="js/cart.js"></script>

</body>
</html>
