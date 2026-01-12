<?php
session_start();
require_once "../admin/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)$_SESSION['user_id'];
$showPopup = false;

/* ===== FETCH USER ===== */
$user = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM users WHERE id=$uid")
);

/* ===== SAVE ADDRESS + PLACE ORDER ===== */
if (isset($_POST['save_address'])) {

    $phone   = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city    = mysqli_real_escape_string($conn, $_POST['city']);
    $state   = mysqli_real_escape_string($conn, $_POST['state']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);

    // ✅ Save address
    mysqli_query($conn, "
        UPDATE users SET
            phone   = '$phone',
            address = '$address',
            city    = '$city',
            state   = '$state',
            pincode = '$pincode'
        WHERE id = $uid
    ");

    // ✅ Place Order (COD)
    $cart = mysqli_query($conn, "
        SELECT pets.*, cart.quantity qty
        FROM cart
        JOIN pets ON pets.id = cart.pet_id
        WHERE cart.user_id = $uid
    ");

    $total = 0;
    $items = [];

    while ($c = mysqli_fetch_assoc($cart)) {
        $c['subtotal'] = $c['price'] * $c['qty'];
        $total += $c['subtotal'];
        $items[] = $c;
    }

    $delivery = ($total < 1499) ? 40 : 0;
    $gst = round($total * 0.12, 2);
    $discount = $_SESSION['discount'] ?? 0;
    $grand = $total + $delivery + $gst - $discount;

    // Insert order
    mysqli_query($conn, "
        INSERT INTO orders
        (user_id, payment_method, total, delivery_charge, gst, discount, grand_total)
        VALUES
        ($uid, 'COD', $total, $delivery, $gst, $discount, $grand)
    ");

    $order_id = mysqli_insert_id($conn);

    // Insert order items
    foreach ($items as $i) {
        mysqli_query($conn, "
            INSERT INTO order_items
            (order_id, pet_id, price, quantity, subtotal)
            VALUES
            ($order_id, {$i['id']}, {$i['price']}, {$i['qty']}, {$i['subtotal']})
        ");
    }

    // Clear cart
    mysqli_query($conn, "DELETE FROM cart WHERE user_id=$uid");
    unset($_SESSION['discount']);

    // 🔥 Show popup
    $showPopup = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Address</title>
    <link rel="stylesheet" href="css/checkout.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="checkout-container">

    <h2>Checkout – Address</h2>

    <form method="post">

        <input type="text" name="phone"
            placeholder="Mobile Number"
            value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>

        <textarea name="address"
            placeholder="Full Address (House No, Area, Landmark)"
            required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>

        <input type="text" name="city"
            placeholder="City"
            value="<?= htmlspecialchars($user['city'] ?? '') ?>" required>

        <input type="text" name="state"
            placeholder="State"
            value="<?= htmlspecialchars($user['state'] ?? '') ?>" required>

        <input type="text" name="pincode"
            placeholder="Pincode"
            value="<?= htmlspecialchars($user['pincode'] ?? '') ?>" required>

        <button type="submit" name="save_address">
            Confirm Address & Place Order
        </button>

    </form>

    <div class="payment-info">
        <strong>Payment Method:</strong> Cash On Delivery (COD)
    </div>

</div>

<!-- ✅ POPUP -->
<?php if ($showPopup): ?>
<div class="popup-overlay">
    <div class="popup-box">
        <h2>🎉 Your Order is Placed</h2>
        <p>Your order has been placed successfully.</p>
        <a href="my_orders.php">Go to My Orders</a>
    </div>
</div>
<?php endif; ?>

</body>
</html>
