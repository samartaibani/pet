<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "petshop");

if(!$conn){
    die("Database connection failed: " . mysqli_connect_error());
}

/* ===== SINGLE ADMIN DETAILS ===== */
define("ADMIN_EMAIL", "admin@gmail.com");
define("ADMIN_PASSWORD", "admin123"); // you can hash later
?>
