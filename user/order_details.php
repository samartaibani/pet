<?php
require_once "../admin/db.php";

$id = (int)($_GET['id'] ?? 0);

$order = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT o.*, u.name, u.email, u.phone, u.address, u.city, u.state, u.pincode
    FROM orders o
    JOIN users u ON u.id=o.user_id
    WHERE o.id=$id
"));
if(!$order) die("Order not found");

$items = mysqli_query($conn,"
    SELECT oi.*, p.pet_name
    FROM order_items oi
    JOIN pets p ON p.id=oi.pet_id
    WHERE oi.order_id=$id
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Order Details</title>
<style>
body{font-family:Arial;background:#f5f6fa;padding:30px}
.box{background:#fff;padding:25px;border-radius:8px;max-width:800px;margin:auto}
table{width:100%;border-collapse:collapse}
th,td{padding:8px;border-bottom:1px solid #ddd;font-size:14px}
</style>
</head>
<body>

<div class="box">
<h2>Order Details</h2>

<p><b>Order ID:</b> #<?= $order['id'] ?></p>
<p><b>Payment:</b> Cash On Delivery</p>
<p><b>Total:</b> ₹<?= $order['grand_total'] ?></p>

<h3>Delivery Address</h3>
<p>
<?= $order['name'] ?><br>
<?= $order['phone'] ?><br>
<?= $order['email'] ?><br>
<?= $order['address'] ?><br>
<?= $order['city'] ?>, <?= $order['state'] ?> - <?= $order['pincode'] ?>
</p>

<h3>Items</h3>
<table>
<tr><th>Pet</th><th>Qty</th><th>Price</th><th>Total</th></tr>
<?php while($i=mysqli_fetch_assoc($items)): ?>
<tr>
<td><?= $i['pet_name'] ?></td>
<td><?= $i['quantity'] ?></td>
<td>₹<?= $i['price'] ?></td>
<td>₹<?= $i['subtotal'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>

</body>
</html>
