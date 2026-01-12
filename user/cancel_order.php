<?php
session_start();
require_once "../admin/db.php";

$id = $_GET['id'];
$uid = $_SESSION['user_id'];

mysqli_query($conn,"
UPDATE orders SET status='Cancelled'
WHERE id=$id AND user_id=$uid AND status='Pending'
");

header("Location: my_orders.php");
