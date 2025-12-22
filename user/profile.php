<?php
session_start();
require_once __DIR__ . "/../admin/db.php";

/* ================= LOGIN CHECK ================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int) $_SESSION['user_id'];

/* ================= FETCH USER ================= */
$user = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM users WHERE id=$id")
);

/* ================= UPDATE PROFILE ================= */
if (isset($_POST['update'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);

    if (!empty($_FILES['image']['name'])) {
        $img = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/$img");
    } else {
        $img = $user['profile_img'];
    }

    mysqli_query($conn, "
        UPDATE users 
        SET username='$username', profile_img='$img'
        WHERE id=$id
    ");

    header("Location: profile.php");
    exit;
}

/* ================= CHANGE PASSWORD ================= */
if (isset($_POST['change_password'])) {

    $current = $_POST['current_password'];
    $new     = $_POST['new_password'];

    if (password_verify($current, $user['password'])) {

        $hash = password_hash($new, PASSWORD_DEFAULT);

        mysqli_query($conn,
            "UPDATE users SET password='$hash' WHERE id=$id"
        );

        $success = true;

    } else {
        $error = "Current password is incorrect";
    }
}

/* ================= DELETE ACCOUNT ================= */
if (isset($_POST['delete_account'])) {

    mysqli_query($conn, "DELETE FROM users WHERE id=$id");

    session_destroy();
    header("Location: register.php");
    exit;
}
?>

<link rel="stylesheet" href="css/profile.css">
<?php include 'navbar.php'; ?>
<div class="auth-form">
    <h2>My Profile</h2>

    <img src="uploads/<?= $user['profile_img'] ?>" alt="Profile Image">

    <!-- UPDATE PROFILE -->
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="username"
               value="<?= htmlspecialchars($user['username']) ?>" required>

        <input type="email"
               value="<?= htmlspecialchars($user['email']) ?>" disabled>

        <input type="file" name="image">

        <button name="update">Update Profile</button>
    </form>

    <!-- CHANGE PASSWORD BUTTON -->
    <button type="button" class="popup-btn" onclick="openPopup()">
        Change Password
    </button>

    <!-- DELETE ACCOUNT -->
    <form method="POST"
          onsubmit="return confirm('Are you sure you want to permanently delete your account?');">
        <button name="delete_account" class="delete-small">
            Delete Account
        </button>
    </form>

    <p><a href="logout.php">Logout</a></p>
</div>

<!-- ================= CHANGE PASSWORD POPUP ================= -->
<div id="passwordPopup" class="popup">
    <div class="popup-content">

        <span class="close" onclick="closePopup()">×</span>

        <h3>Change Password</h3>

        <?php if (isset($error)) { ?>
            <p class="error"><?= $error ?></p>
        <?php } ?>

        <form method="POST">

            <div class="password-box">
                <input type="password" id="current_password"
                       name="current_password"
                       placeholder="Current Password" required>
                <span onclick="togglePassword('current_password', this)">👁️</span>
            </div>

            <div class="password-box">
                <input type="password" id="new_password"
                       name="new_password"
                       placeholder="New Password" required>
                <span onclick="togglePassword('new_password', this)">👁️</span>
            </div>

            <button name="change_password">
                Update Password
            </button>
        </form>

        <!-- SUCCESS MESSAGE -->
        <div id="successBox" class="success-box">
            <div class="checkmark">✔</div>
            <p>Password changed successfully</p>
        </div>

    </div>
</div>

<script>
function openPopup(){
    document.getElementById("passwordPopup").classList.add("show");
}

function closePopup(){
    document.getElementById("passwordPopup").classList.remove("show");
}

function togglePassword(id, el){
    const input = document.getElementById(id);
    if (input.type === "password") {
        input.type = "text";
        el.innerHTML = "🙈";
    } else {
        input.type = "password";
        el.innerHTML = "👁️";
    }
}

/* ===== SUCCESS HANDLING AFTER PAGE RELOAD ===== */
<?php if (isset($success)) { ?>
    openPopup();

    setTimeout(() => {
        document.getElementById("successBox").classList.add("show");
    }, 200);

    setTimeout(() => {
        closePopup();
        document.getElementById("successBox").classList.remove("show");
    }, 2200);
<?php } ?>

/* ===== ERROR HANDLING AFTER PAGE RELOAD ===== */
<?php if (isset($error)) { ?>
    openPopup();
<?php } ?>
</script>
