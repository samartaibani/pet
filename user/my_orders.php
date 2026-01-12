<?php
session_start();
require_once "../admin/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

$orders = mysqli_query($conn, "
    SELECT * FROM orders
    WHERE user_id = $uid
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="css/my_orders.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="page-layout">
 

    <!-- RIGHT CONTENT -->
     <div class="right-content">
        <h2>My Orders</h2>

        <?php if (mysqli_num_rows($orders) == 0): ?>
            <p>No orders found.</p>
        <?php endif; ?>

        <?php while ($o = mysqli_fetch_assoc($orders)): ?>

            <div class="order-box">

                <h4>Order #<?= $o['id'] ?></h4>

                <p><strong>Date:</strong> <?= $o['created_at'] ?></p>

                <p><strong>Total:</strong> ₹<?= $o['grand_total'] ?></p>

                <p class="status">
                    <strong>Status:</strong> <?= $o['status'] ?>
                </p>

                <p><strong>Payment:</strong> COD</p>

                <div class="order-actions">

                    <a href="invoice.php?id=<?= $o['id'] ?>">Download Invoice</a>
                    <?php if ($o['status'] == 'Pending'): ?>
                        <a href="cancel_order.php?id=<?= $o['id'] ?>" class="cancel">
                            Cancel
                        </a>
                    <?php endif; ?>

                </div>

            </div>

        <?php endwhile; ?>
    </div>

</div>

</body>
</html>
