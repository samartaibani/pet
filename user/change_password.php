<?php
session_start();
require_once "../admin/db.php";
$id=$_SESSION['user_id'];

if(isset($_POST['change'])){
    $old=$_POST['old'];
    $new=password_hash($_POST['new'],PASSWORD_DEFAULT);

    $u=mysqli_fetch_assoc(mysqli_query($conn,"SELECT password FROM users WHERE id=$id"));
    if(password_verify($old,$u['password'])){
        mysqli_query($conn,"UPDATE users SET password='$new' WHERE id=$id");
        mysqli_query($conn,"INSERT INTO notifications (user_id,title,message)
        VALUES ($id,'Password Changed','Your password was updated')");
        $msg="Password updated";
    }else $err="Wrong old password";
}
?>

    <link rel="stylesheet" href="css/change_password.css?v=<?= time() ?>">  
    <?php include "navbar.php"; ?>
    <div class="page-layout">
        <div class="right-content">
            <div class="profile-container">
                <h2>Change Password</h2>
                <form method="post">
                    <input type="password" name="old" placeholder="Old Password" required>
                    <input type="password" name="new" placeholder="New Password" required>
                    <button name="change">Change</button>
                </form>
                <?= $msg ?? $err ?? "" ?>
            </div>
        </div>
    </div>