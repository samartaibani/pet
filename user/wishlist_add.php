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

    // Duplicate check
    $check = mysqli_query($conn,
        "SELECT id FROM wishlist WHERE user_id=$user_id AND pet_id=$pet_id"
    );

    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn,
            "INSERT INTO wishlist (user_id, pet_id) VALUES ($user_id, $pet_id)"
        );
    }
}

header("Location: pets.php");
exit;
