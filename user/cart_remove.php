<?php
session_start();
require_once __DIR__ . "/../admin/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$pet_id  = (int) ($_GET['id'] ?? 0);

mysqli_query($conn,
    "DELETE FROM cart WHERE user_id=$user_id AND pet_id=$pet_id"
);

header("Location: cart.php");
exit;
