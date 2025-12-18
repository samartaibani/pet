<?php
include "config.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>

<link rel="stylesheet" href="css/navbar.css">

<div class="sidebar">
    <h1 class="logo">🐾 PetShop</h1>

    <a href="index.php">Home</a>
    <a href="pets_crud.php">Add Pet</a>
    <a href="categories.php">Category</a>
    <a href="booking.php">Booking</a>
    <a href="users.php">Users</a>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>
</div>
