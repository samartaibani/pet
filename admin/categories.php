
 <?php
include "db.php";

// ================= ADD CATEGORY =================
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$name')");
    header("Location: categories.php");
}

// ================= DELETE CATEGORY =================
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
    header("Location: categories.php");
}

// ================= FETCH CATEGORY FOR EDIT =================
$edit_id = "";
$edit_name = "";

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM categories WHERE id=$edit_id");
    $row = mysqli_fetch_assoc($res);
    $edit_name = $row['name'];
}

// ================= UPDATE CATEGORY =================
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    mysqli_query($conn, "UPDATE categories SET name='$name' WHERE id=$id");
    header("Location: categories.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin | Categories</title>
    <link rel="stylesheet" href="css/categories.css?v=<?php echo time(); ?>">
</head>

<body>
     <?php include "navbar.php"; ?>
<div class="content">
    <div class="topbar">
        <h2>📂 Category Management</h2>
        <a href="./add_breed.php" class="add-breed-btn">➕ Add Breed</a>
    </div>

<!-- ================= ADD / EDIT FORM ================= -->
<form method="post">
    <input type="text" name="name" placeholder="Category Name" 
           value="<?= $edit_name ?>" required>

    <?php if ($edit_id) { ?>
        <input type="hidden" name="id" value="<?= $edit_id ?>">
        <button name="update">Update Category</button>
        <a href="categories.php">Cancel</a>
    <?php } else { ?>
        <button name="add">Add Category</button>
    <?php } ?>
</form>

<br><br>

<!-- ================= CATEGORY LIST ================= -->
<table>
<tr>
    <th>Name</th>
    <th>Action</th>
</tr>

<?php
$result = mysqli_query($conn, "SELECT * FROM categories");
while ($row = mysqli_fetch_assoc($result)) {
?>
<tr>

    <td><?= $row['name']; ?></td>
    <td>
        <a href="categories.php?edit=<?= $row['id']; ?>">Edit</a> |
        <a href="categories.php?delete=<?= $row['id']; ?>"
           onclick="return confirm('Delete this category?')">Delete</a>
    </td>
</tr>

<?php } ?>

</table>

</div>
</body>
</html>
