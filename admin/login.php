<?php include "config.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body { font-family: Arial; background:#f4f4f4; }
        .box {
            width: 300px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px #ccc;
        }
        input, button {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
        }
        button { background: #333; color: white; border: none; }
        .error { color: red; }
    </style>
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
