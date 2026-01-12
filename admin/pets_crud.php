<?php
include "db.php";

/* ================= DELETE ================= */
if (isset($_GET['delete'])) {
    mysqli_query($conn, "DELETE FROM pets WHERE id=".(int)$_GET['delete']);
    header("Location: pets_crud.php");
    exit;
}

/* ================= SELL ================= */
if (isset($_GET['sell'])) {
    $id = (int)$_GET['sell'];

    $check = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT quantity FROM pets WHERE id=$id")
    );

    if ($check && $check['quantity'] > 0) {
        mysqli_query($conn, "UPDATE pets SET quantity = quantity - 1 WHERE id=$id");
        mysqli_query($conn, "UPDATE pets SET status = IF(quantity=0,0,1) WHERE id=$id");
        header("Location: pets_crud.php");
        exit;
    } else {
        echo "<script>alert('Out of Stock');</script>";
    }
}

/* ================= EDIT FETCH ================= */
$edit = false;
$data = [];

if (isset($_GET['edit'])) {
    $edit = true;
    $id = (int)$_GET['edit'];
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pets WHERE id=$id"));
}

/* ================= CATEGORY ================= */
$selected_category = $_POST['category_id'] ?? ($data['category_id'] ?? '');

/* ================= SAVE ================= */
if (isset($_POST['save'])) {

    $category_id = $_POST['category_id'];
    $breed_id    = $_POST['breed_id'];
    $pet_name    = $_POST['pet_name'];
    $age         = $_POST['age'];
    $pet_size    = $_POST['pet_size'];
    $gender      = $_POST['gender'];
    $price       = $_POST['price'];
    $quantity    = $_POST['quantity'];
    $description = $_POST['description'];

    if (!empty($_FILES['image']['name'])) {
        $image = time()."_".$_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/$image");
    } else {
        $image = $_POST['old_image'] ?? '';
    }

    if (!empty($_POST['id'])) {
        mysqli_query($conn, "UPDATE pets SET
            category_id='$category_id',
            breed_id='$breed_id',
            pet_name='$pet_name',
            age='$age',
            pet_size='$pet_size',
            gender='$gender',
            price='$price',
            quantity='$quantity',
            image='$image',
            description='$description'
            WHERE id=".$_POST['id']);
    } else {
        mysqli_query($conn, "INSERT INTO pets
        (category_id, breed_id, pet_name, age, pet_size, gender, price, quantity, image, description)
        VALUES
        ('$category_id','$breed_id','$pet_name','$age','$pet_size','$gender','$price','$quantity','$image','$description')");
    }

    header("Location: pets_crud.php");
    exit;
}

/* ================= DROPDOWNS ================= */
$categories = mysqli_query($conn, "SELECT * FROM categories");

$breeds = $selected_category
    ? mysqli_query($conn, "SELECT * FROM breeds WHERE category_id='$selected_category'")
    : [];

/* ================= PET LIST ================= */
$pets = mysqli_query($conn, "
SELECT p.*, c.name AS category_name, b.breed_name
FROM pets p
JOIN categories c ON p.category_id = c.id
JOIN breeds b ON p.breed_id = b.id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pet CRUD</title>
    <link rel="stylesheet" href="css/pet.css?v=<?php echo time(); ?>">
</head>
<body>
<?php include "navbar.php"; ?>
<div class="content">
<h2><?= $edit ? "Edit Pet" : "Add Pet" ?></h2>

<form method="post" enctype="multipart/form-data">

<?php if ($edit): ?>
    <input type="hidden" name="id" value="<?= $data['id'] ?>">
    <input type="hidden" name="old_image" value="<?= $data['image'] ?>">
<?php endif; ?>

<select name="category_id" onchange="this.form.submit()" required>
    <option value="">Select Category</option>
    <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
        <option value="<?= $cat['id'] ?>" <?= ($selected_category == $cat['id']) ? "selected" : "" ?>>
            <?= $cat['name'] ?>
        </option>
    <?php } ?>
</select>

<select name="breed_id" required>
    <option value="">Select Breed</option>
    <?php if ($breeds) while ($br = mysqli_fetch_assoc($breeds)) { ?>
        <option value="<?= $br['id'] ?>" <?= (($data['breed_id'] ?? '') == $br['id']) ? "selected" : "" ?>>
            <?= $br['breed_name'] ?>
        </option>
    <?php } ?>
</select>

<input type="text" name="pet_name" placeholder="Pet Name" value="<?= $data['pet_name'] ?? '' ?>" required>
<input type="text" name="age" placeholder="Age" value="<?= $data['age'] ?? '' ?>">

<select name="pet_size" required>
    <option value="">Select Size</option>
    <?php foreach (['Small','Medium','Large'] as $s) { ?>
        <option <?= (($data['pet_size'] ?? '')==$s)?"selected":"" ?>><?= $s ?></option>
    <?php } ?>
</select>

<select name="gender">
    <option <?= (($data['gender'] ?? '')=="Male")?"selected":"" ?>>Male</option>
    <option <?= (($data['gender'] ?? '')=="Female")?"selected":"" ?>>Female</option>
</select>

<input type="number" name="price" placeholder="Price" value="<?= $data['price'] ?? '' ?>">
<input type="number" name="quantity" value="<?= $data['quantity'] ?? 1 ?>">

<input type="file" name="image">

<?php if (!empty($data['image'])): ?>
    <img src="uploads/<?= $data['image'] ?>" width="70">
<?php endif; ?>

<textarea name="description" placeholder="Description"><?= $data['description'] ?? '' ?></textarea>

<button name="save"><?= $edit ? "Update Pet" : "Add Pet" ?></button>

<?php if ($edit): ?>
    <a href="pets_crud.php">Cancel</a>
<?php endif; ?>

</form>
<br><br><br>

<h2>Pet List</h2>

<table>
<tr>
    
    <th>Category</th>
    <th>Breed</th>
    <th>Pet</th>
    <th>Qty</th>
    <th>Action</th>
</tr>

<?php while ($p = mysqli_fetch_assoc($pets)) { ?>
<tr>
    
    <td><?= $p['category_name'] ?></td>
    <td><?= $p['breed_name'] ?></td>
    <td><?= $p['pet_name'] ?></td>
    <td><?= $p['quantity'] ?></td>
    <td>
        <a href="?edit=<?= $p['id'] ?>">Edit</a> |
        <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Delete?')">Delete</a> |
        <?php if ($p['quantity'] > 0): ?>
            <a href="?sell=<?= $p['id'] ?>">Sell</a>
        <?php else: ?>
            <span style="color:red;">Out</span>
        <?php endif; ?>
    </td>
</tr>
<?php } ?>
</table>

</div>
</body>
</html>
