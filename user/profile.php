<?php
session_start();
require_once "../admin/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int) $_SESSION['user_id'];
$error = '';
$success = '';

/* ================= UPDATE PROFILE ================= */
if (isset($_POST['update'])) {

    $username = trim($_POST['username']);

    if ($username === '') {
        $error = "Username cannot be empty";
    } else {

        // 🔴 CHECK DUPLICATE USERNAME (EXCEPT CURRENT USER)
        $check = mysqli_query(
            $conn,
            "SELECT id FROM users 
             WHERE username='" . mysqli_real_escape_string($conn, $username) . "' 
             AND id != $id"
        );

        if (mysqli_num_rows($check) > 0) {
            $error = "Username already taken. Please choose another.";
        } else {

            // ✅ IMAGE UPLOAD (OPTIONAL)
            if (!empty($_FILES['profile_img']['name'])) {

                $img = time() . '_' . basename($_FILES['profile_img']['name']);
                $uploadPath = "uploads/" . $img;

                if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $uploadPath)) {

                    mysqli_query(
                        $conn,
                        "UPDATE users 
                         SET username='" . mysqli_real_escape_string($conn, $username) . "',
                             profile_img='$img'
                         WHERE id=$id"
                    );

                    $_SESSION['profile_img'] = $img;
                }

            } else {

                mysqli_query(
                    $conn,
                    "UPDATE users 
                     SET username='" . mysqli_real_escape_string($conn, $username) . "'
                     WHERE id=$id"
                );
            }

            $_SESSION['username'] = $username;
            $success = "Profile updated successfully";
        }
    }
}

/* ================= FETCH USER ================= */
$user = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM users WHERE id=$id")
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="css/profile.css?v=<?= time() ?>">
</head>

<?php include 'navbar.php' ?>

<body>

<div class="page-layout">
    <!-- RIGHT CONTENT -->
    <div class="right-content">
        <div class="profile-container">

            <h2>My Profile</h2>

            <!-- MESSAGES -->
            <?php if ($error): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p class="success"><?= $success ?></p>
            <?php endif; ?>

            <img
                src="uploads/<?= htmlspecialchars($user['profile_img'] ?: 'default.png') ?>"
                class="profile-img"
                alt="Profile Image"
            >

            <form method="post" enctype="multipart/form-data">

                <input
                    type="text"
                    name="username"
                    value="<?= htmlspecialchars($user['username']) ?>"
                    required
                >

                <input
                    type="email"
                    value="<?= htmlspecialchars($user['email']) ?>"
                    disabled
                >

                <input type="file" name="profile_img">

                <button type="submit" name="update">Update</button>

            </form>

        </div>
    </div>

</div>

</body>
</html>
