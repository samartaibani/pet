<?php
session_start();
require_once __DIR__ . "/../admin/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$pet_id  = (int) ($_GET['id'] ?? 0);
$action  = $_GET['action'] ?? '';

if ($pet_id > 0) {

    if ($action === 'plus') {
        mysqli_query($conn,
            "UPDATE cart SET quantity = quantity + 1 
             WHERE user_id=$user_id AND pet_id=$pet_id"
        );
    }

    if ($action === 'minus') {

        $q = mysqli_fetch_assoc(
            mysqli_query($conn,
                "SELECT quantity FROM cart WHERE user_id=$user_id AND pet_id=$pet_id"
            )
        );

        if ($q && $q['quantity'] > 1) {
            mysqli_query($conn,
                "UPDATE cart SET quantity = quantity - 1 
                 WHERE user_id=$user_id AND pet_id=$pet_id"
            );
        } else {
            // quantity = 1 → remove item
            mysqli_query($conn,
                "DELETE FROM cart WHERE user_id=$user_id AND pet_id=$pet_id"
            );
        }
    }
}

/* 🔁 stay on cart page */
header("Location: cart.php");
exit;
