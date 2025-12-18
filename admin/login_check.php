<?php
include "config.php";

$email = $_POST['email'];
$pass  = $_POST['password'];

if ($email === ADMIN_EMAIL && $pass === ADMIN_PASSWORD) {
    $_SESSION['admin'] = true;
    header("Location: index.php");
} else {
    header("Location: login.php?error=1");
}
?>
