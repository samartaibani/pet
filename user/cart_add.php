<?php
session_start();
require_once __DIR__ . "/../admin/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$pet_id  = (int) ($_GET['id'] ?? 0);

if ($pet_id > 0) {

    $check = mysqli_fetch_assoc(
        mysqli_query($conn,
            "SELECT quantity FROM cart WHERE user_id=$user_id AND pet_id=$pet_id"
        )
    );

    if ($check) {
        mysqli_query($conn,
            "UPDATE cart 
             SET quantity = quantity + 1 
             WHERE user_id=$user_id AND pet_id=$pet_id"
        );
    } else {
        mysqli_query($conn,
            "INSERT INTO cart (user_id, pet_id, quantity) VALUES ($user_id, $pet_id, 1)"
        );
    }
}

/* 🔥 SAME PAGE RETURN (NO REDIRECT JUMP) */
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
