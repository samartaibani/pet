<?php
include "db.php";

/* ===== UPDATE SHIPPING ===== */
if(isset($_POST['update'])){
    $id = (int)$_POST['id'];
    $ship = $_POST['shipping_status'];

    mysqli_query($conn,"UPDATE orders SET shipping_status='$ship' WHERE id=$id");

    $o = mysqli_fetch_assoc(mysqli_query($conn,"SELECT payment_method FROM orders WHERE id=$id"));

    if($o['payment_method']=='COD' && $ship=='Collected'){
        mysqli_query($conn,"UPDATE orders SET payment_status='Paid' WHERE id=$id");
    }

    if($o['payment_method']!='COD'){
        mysqli_query($conn,"UPDATE orders SET payment_status='Paid' WHERE id=$id");
    }

    header("Location: orders.php");
    exit;
}

/* ===== FETCH ===== */
$orders = mysqli_query($conn,"
SELECT o.*, u.username 
FROM orders o
JOIN users u ON o.user_id=u.id
ORDER BY o.id DESC
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Orders</title>
<link rel="stylesheet" href="css/orders.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="content">

<div class="topbar">
<h2>📦 Orders</h2>
</div>

<table>
<tr>
<th>User</th>
<th>Total</th>
<th>Payment</th>
<th>Pay Status</th>
<th>Shipping</th>
<th>Action</th>
</tr>

<?php while($o=mysqli_fetch_assoc($orders)){
$lock = ($o['shipping_status']=="Collected");
?>
<tr class="<?= $lock?'locked':'' ?>">

<td><?= $o['username'] ?></td>
<td>₹<?= $o['grand_total'] ?></td>
<td><?= $o['payment_method'] ?></td>
<td><?= $o['payment_status'] ?></td>

<td>
<form method="post">
<input type="hidden" name="id" value="<?= $o['id'] ?>">
<select name="shipping_status" <?= $lock?'disabled':'' ?>>
<?php foreach(['Pending','Packing','Shipping','Delivered','Collected'] as $s){ ?>
<option <?= ($o['shipping_status']==$s)?'selected':'' ?>><?= $s ?></option>
<?php } ?>
</select>
</td>

<td>
<?php if(!$lock){ ?>
<button name="update">Update</button>
<?php } else { ?>
<span class="done">✔ Completed</span>
<?php } ?>
</form>
</td>
</tr>
<?php } ?>
</table>

</div>
</body>
</html>
