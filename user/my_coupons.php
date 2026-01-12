<?php
session_start();
include "../admin/db.php";

$uid = $_SESSION['user_id'];

$coupons = mysqli_query($conn,"
SELECT c.code, c.type, c.value, c.min_amount, uc.is_used, uc.message
FROM user_coupons uc
JOIN coupons c ON uc.coupon_code = c.code
WHERE uc.user_id = $uid
");
?>
<link rel="stylesheet" href="css/coupons.css">
<?php include 'navbar.php' ?>

<div class="page-layout">

    <div class="right-content">
        <h2>🎁 My Coupons</h2>
        <table border="1" width="100%">
        <tr>
            <th>Code</th>
            <th>Message</th>
            <th>Discount</th>
            <th>Min Order</th>
            <th>Status</th>
        </tr>

        <?php while($c=mysqli_fetch_assoc($coupons)){ ?>
        <tr>
            <td><?= $c['code'] ?></td>
            <td><?= $c['message'] ?></td>
            <td>
                <?= $c['type']=='flat'
                    ? "₹".$c['value']
                    : $c['value']."%" ?>
            </td>
            <td>₹<?= $c['min_amount'] ?></td>
            <td><?= $c['is_used'] ? "Used" : "Available" ?></td>
        </tr>
        <?php } ?>
        </table>
    </div>
</div>
