<?php
include "config.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

/* ================== COUNTS ================== */

// Total Pets
$q1 = mysqli_query($conn,"SELECT COUNT(*) AS total FROM pets");
$totalPets = mysqli_fetch_assoc($q1)['total'];

// Total Users
$q2 = mysqli_query($conn,"SELECT COUNT(*) AS total FROM users");
$totalUsers = mysqli_fetch_assoc($q2)['total'];

// Total Orders
$q3 = mysqli_query($conn,"SELECT COUNT(*) AS total FROM orders");
$totalOrders = mysqli_fetch_assoc($q3)['total'];

// Total Revenue (Delivered orders only)
$q4 = mysqli_query($conn,"SELECT SUM(total_amount) AS sum FROM orders WHERE status='Delivered'");
$rev = mysqli_fetch_assoc($q4);
$revenue = $rev['sum'] ?? 0;

/* ================== LATEST ORDERS ================== */
$latestOrders = mysqli_query($conn,"
    SELECT o.total_amount, o.status, u.username
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.id DESC
    LIMIT 5
");

/* ================== LOW STOCK PETS ================== */
/*
IMPORTANT:
Replace `pet_name` below if your pets table has a different column name
(like title, product_name, etc.)
*/
$lowStock = mysqli_query($conn,"
    SELECT pet_name AS name, stock
    FROM pets
    WHERE stock <= 3
    ORDER BY stock ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="content">

    <h2>Welcome Admin 👋</h2>

    <div class="cards">
        <div class="card">
            <h3>🐾 Pets</h3>
            <p><?= $totalPets ?></p>
        </div>

        <div class="card">
            <h3>👥 Users</h3>
            <p><?= $totalUsers ?></p>
        </div>

        <div class="card">
            <h3>📦 Orders</h3>
            <p><?= $totalOrders ?></p>
        </div>

    </div>

</div>

</body>
</html>
