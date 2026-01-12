<?php
require_once __DIR__ . "/db.php";

/* ================= ADD BREED ================= */
if (isset($_POST['add'])) {
    $category_id = $_POST['category_id'];
    $breed_name  = $_POST['breed_name'];

    mysqli_query($conn,
        "INSERT INTO breeds (category_id, breed_name)
         VALUES ('$category_id', '$breed_name')"
    );
    header("Location: add_breed.php");
    exit;
}

/* ================= DELETE BREED ================= */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM breeds WHERE id=$id");
    header("Location: add_breed.php");
    exit;
}

/* ================= EDIT BREED ================= */
$edit_id = "";
$edit_breed = "";
$edit_category = "";

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM breeds WHERE id=$edit_id");
    $row = mysqli_fetch_assoc($res);

    $edit_breed    = $row['breed_name'];
    $edit_category = $row['category_id'];
}

/* ================= UPDATE BREED ================= */
if (isset($_POST['update'])) {
    $id          = $_POST['id'];
    $category_id = $_POST['category_id'];
    $breed_name  = $_POST['breed_name'];

    mysqli_query($conn,
        "UPDATE breeds 
         SET category_id='$category_id', breed_name='$breed_name'
         WHERE id=$id"
    );
    header("Location: add_breed.php");
    exit;
}
?>
   
<!DOCTYPE html>
<html>
<head>
    <title>Manage Breeds</title>
    <link rel="stylesheet" href="css/breeds.css?v=<?php echo time(); ?>">
</head>

<body>

    <?php include "navbar.php"; ?>
<div class="content">
    <div class="topbar">
        <h2>🐶 Breed Management</h2>
    </div>

<!-- ================= ADD / EDIT FORM ================= -->
<form method="post">
    <h3><?= $edit_id ? "Edit Breed" : "Add Breed" ?></h3>

    <select class="category_id" name="category_id" required>
        <option value="">Select Category</option>
        <?php
        $cats = mysqli_query($conn, "SELECT * FROM categories");
        while ($c = mysqli_fetch_assoc($cats)) {
            $sel = ($c['id'] == $edit_category) ? "selected" : "";
            echo "<option value='{$c['id']}' $sel>{$c['name']}</option>";
        }
        ?>
    </select>

    <input type="text" name="breed_name"
           value="<?= $edit_breed ?>"
           placeholder="Breed Name" required>

    <?php if ($edit_id) { ?>
        <input type="hidden" name="id" value="<?= $edit_id ?>">
        <button name="update">Update Breed</button>
        <a href="add_breed.php" class="cancel-btn">Cancel</a>
    <?php } else { ?>
        <button name="add">Add Breed</button>
    <?php } ?>
</form>

<!-- ================= CATEGORY + BREED TABLE ================= -->
<table>
<tr>
    <th>Category</th>
    <th>Breed</th>
    <th>Action</th>
</tr>

<?php
$categories = mysqli_query($conn, "SELECT * FROM categories");

while ($cat = mysqli_fetch_assoc($categories)) {

    $breeds = mysqli_query($conn,
        "SELECT * FROM breeds WHERE category_id=".$cat['id']
    );

    if (mysqli_num_rows($breeds) > 0) {
        while ($b = mysqli_fetch_assoc($breeds)) {
            echo "<tr>
                    <td class='cat'>{$cat['name']}</td>
                    <td>{$b['breed_name']}</td>
                    <td>
                        <a class='edit' href='add_breed.php?edit={$b['id']}'>Edit</a> |
                        <a class='delete' href='add_breed.php?delete={$b['id']}'
                           onclick=\"return confirm('Delete this breed?')\">Delete</a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr>
                <td class='cat'>{$cat['name']}</td>
                <td colspan='2'><em>No breeds</em></td>
              </tr>";
    }
}
?>

</table>
</div>

</body>
</html>
