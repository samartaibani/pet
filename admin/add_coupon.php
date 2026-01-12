<?php
session_start();
require_once __DIR__ . "/db.php";

$msg = "";

/* ===== FUNCTIONS ===== */
function generateCouponCode($length = 8) {
    return strtoupper(substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, $length));
}

function generateCouponValue() {
    return rand(100, 500); // 
}

function generateMinOrderAmount() {
    $options = [999, 1199, 1499, 1999, 2499, 3499 ];
    return $options[array_rand($options)];
}

/* ===== ADD MANUAL COUPON ===== */
if (isset($_POST['add_coupon'])) {
    $code = strtoupper(trim($_POST['code']));
    $type = $_POST['type']; // flat / percent
    $value = (int)$_POST['value'];
    $min_amount = (int)$_POST['min_amount'];

    mysqli_query($conn,"
        INSERT INTO coupons (code,type,value,min_amount,status)
        VALUES ('$code','$type',$value,$min_amount,1)
    ");

    $msg = "Coupon added successfully";
}

/* ===== AUTO GENERATE COUPON ===== */
if (isset($_POST['auto_generate'])) {
    $code = generateCouponCode();
    $value = generateCouponValue();
    $min_amount = generateMinOrderAmount();

    mysqli_query($conn,"
        INSERT INTO coupons (code,type,value,min_amount,status)
        VALUES ('$code','flat',$value,$min_amount,1)
    ");

    $msg = "Auto coupon generated: $code | ₹$value OFF | Min ₹$min_amount";
}

/* ===== DELETE COUPON ===== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn,"DELETE FROM coupons WHERE id=$id");
    header("Location: add_coupon.php");
    exit;
}

/* ===== TOGGLE STATUS ===== */
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    mysqli_query($conn,"
        UPDATE coupons
        SET status = IF(status=1,0,1)
        WHERE id=$id
    ");
    header("Location: add_coupon.php");
    exit;
}

/* ===== ASSIGN COUPON TO USER ===== */
if (isset($_POST['assign_coupon'])) {
    $user_id = (int)$_POST['user_id'];
    $coupon_code = $_POST['coupon_code'];

    mysqli_query($conn,"
        INSERT INTO user_coupons (user_id,coupon_code,message)
        VALUES ($user_id,'$coupon_code','🎁 Your personal coupon')
    ");

    $msg = "Coupon assigned to user successfully";
}

/* ===== FETCH DATA ===== */
$coupons = mysqli_query($conn,"SELECT * FROM coupons ORDER BY id DESC");
$users   = mysqli_query($conn,"SELECT id, username FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Coupon Panel</title>
    <link rel="stylesheet" href="css/coupon.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="content">
    <div class="topbar">
        <h2>🏷️ Coupon Management</h2>
    </div>
<?php if ($msg) { ?>
    <div class="msg"><?= $msg ?></div>
<?php } ?>

<form method="post" class="form">
    <input type="text" name="code" placeholder="Coupon Code (SAVE50)" required>

    <select name="type">
        <option value="flat">Flat Discount (₹)</option>
        <option value="percent">Percent (%)</option>
    </select>

    <input type="number" name="value" placeholder="Discount Value" required>
    <input type="number" name="min_amount" placeholder="Minimum Order Value (1000+)" required>

    <button name="add_coupon">Add Coupon</button>
</form>

<h2>Auto Generate Coupon</h2>

<form method="post">
    <button name="auto_generate" class="auto-btn">
        Generate Coupon
    </button>
</form>

<h2>All Coupons</h2>

<table>
<tr>
    <th>Code</th>
    <th>Type</th>
    <th>Value</th>
    <th>Min Order</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while ($c = mysqli_fetch_assoc($coupons)) { ?>
<tr>
    <td><?= $c['code'] ?></td>
    <td><?= $c['type'] ?></td>
    <td>₹<?= $c['value'] ?></td>
    <td>₹<?= $c['min_amount'] ?></td>
    <td><?= $c['status'] ? 'Active' : 'Inactive' ?></td>
    <td>
        <a href="?toggle=<?= $c['id'] ?>" class="btn">
            <?= $c['status'] ? 'OFF' : 'ON' ?>
        </a>
        <a href="?delete=<?= $c['id'] ?>" class="btn danger"
           onclick="return confirm('Delete this coupon?')">
            Delete
        </a>
    </td>
</tr>
<?php } ?>
</table>

<h2>Assign Coupon to User</h2>

<form method="post" class="form">
    <select name="user_id" required>
        <?php while ($u = mysqli_fetch_assoc($users)) { ?>
            <option value="<?= $u['id'] ?>">
                <?= $u['username'] ?>
            </option>
        <?php } ?>
    </select>

    <input type="text" name="coupon_code" placeholder="Coupon Code" required>

    <button name="assign_coupon">Assign Coupon</button>
</form>

</div>

</body>
</html>
