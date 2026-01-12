<?php
include "db.php";

/* ===== DELETE USER ===== */
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];

    $u = mysqli_fetch_assoc(mysqli_query($conn,"SELECT profile_img FROM users WHERE id=$id"));

    if(!empty($u['profile_img'])){
        @unlink("../user/uploads/".$u['profile_img']);
    }

    mysqli_query($conn,"DELETE FROM users WHERE id=$id");
    header("Location: users.php");
    exit;
}

/* ===== FETCH USERS ===== */
$users = mysqli_query($conn,"SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link rel="stylesheet" href="css/users.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="content">

<div class="topbar">
    <h2>👥 Users</h2>
</div>

<table>
<tr>
    <th>Photo</th>
    <th>Name</th>
    <th>Email</th>
    <th>Joined</th>
    <th>Action</th>
</tr>

<?php while($u=mysqli_fetch_assoc($users)){ ?>
<tr>
    <td>
        <img src="../user/uploads/<?= $u['profile_img'] ?: 'default.png' ?>" class="avatar">
    </td>
    <td><?= $u['username'] ?></td>
    <td><?= $u['email'] ?></td>
    <td><?= date("d M Y",strtotime($u['created_at'])) ?></td>
    <td>
        <a href="?delete=<?= $u['id'] ?>" class="del"
           onclick="return confirm('Delete this user?')">Delete</a>
    </td>
</tr>
<?php } ?>

</table>
</div>

</body>
</html>
