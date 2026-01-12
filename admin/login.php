<?php include "config.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/login.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="box">
    <h2>Admin Login</h2>

    <?php if(isset($_GET['error'])) echo "<p class='error'>Invalid Login</p>"; ?>

    <form method="post" action="login_check.php">
        <input type="email" name="email" placeholder="Admin Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
