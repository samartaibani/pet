<?php
session_start();
require_once "../admin/db.php";
$uid = $_SESSION['user_id'];

/* Fetch cart */
$cart = mysqli_query($conn,"
SELECT pets.*, cart.quantity qty
FROM cart JOIN pets ON pets.id=cart.pet_id
WHERE cart.user_id=$uid
");

$total = 0;
$items = [];

while($c = mysqli_fetch_assoc($cart)){
    $c['subtotal'] = $c['price'] * $c['qty'];
    $total += $c['subtotal'];
    $items[] = $c;
}

$delivery = ($total < 1499) ? 40 : 0;
$gst = round($total * 0.12, 2);
$discount = $_SESSION['discount'] ?? 0;
$grand = $total + $delivery + $gst - $discount;

/* Insert order */
mysqli_query($conn,"
INSERT INTO orders
(user_id,payment_method,total,delivery_charge,gst,discount,grand_total)
VALUES
($uid,'COD',$total,$delivery,$gst,$discount,$grand)
");

$order_id = mysqli_insert_id($conn);

/* Insert items */
foreach($items as $i){
    mysqli_query($conn,"
        INSERT INTO order_items(order_id,pet_id,price,quantity,subtotal)
        VALUES($order_id,{$i['id']},{$i['price']},{$i['qty']},{$i['subtotal']})
    ");

    mysqli_query($conn,"
        UPDATE pets SET quantity = quantity - {$i['qty']} WHERE id = {$i['id']}
    ");
}

/* Clear cart */
mysqli_query($conn,"DELETE FROM cart WHERE user_id=$uid");
unset($_SESSION['discount']);

header("Location: order_success.php?id=$order_id");
exit;
?>