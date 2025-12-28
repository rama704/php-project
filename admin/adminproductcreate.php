<?php
require_once '../includes/db.connection.php';
require_once 'admin_auth.php';

$db = Database::getInstance();
$conn = $db->getConnection();

$categoriesQuery = "SELECT * FROM categories ORDER BY name ASC";
$categoriesResult = mysqli_query($conn, $categoriesQuery);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $discount = $_POST['discount_price'] ?: null;
    $category_id = $_POST['category_id'];
    $stock = $_POST['stock'];
    // عشان لو ما كان في صورة وعملت ادخال لبيانات جديدة ما يعطيني خطأ تكون الصورة فاضية
    $image = null;
    // عشان اتحقق انو في صورة موجود عنجد وكمان مرفوعة بنجاح مافي اي ايرور
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageName = time() . '_' . $_FILES['image']['name'];
                $uploadimage = '../images/';
                $targetPath = 'images/' . $imageName; // نحفظ الصورة هنا
        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
        $image = $imageName; 
    }

    // الاستعلام
    $query = "INSERT INTO products(category_id, name, description, price, discount_price, stock, image)
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param($stmt, "issddis", 
        $category_id,
        $name,
        $description,
        $price,
        $discount,
        $stock,
        $image
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: adminproducts.php?created=1");
        exit;
    } else {
        $error = "Error: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <link rel="stylesheet" href="adminproductcreate.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="container">
    <h2>Add New Product</h2>

    <?php if (isset($error)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Product Name:</label>
        <input type="text" name="name" placeholder="Enter product name" required>

        <label>Description:</label>
        <textarea name="description" placeholder="Enter product description" rows="4" required></textarea>

        <label>Price:</label>
        <input type="number" name="price" placeholder="Enter product price" step="0.01" required>

        <label>Discount Price:</label>
        <input type="number" name="discount_price" placeholder="Enter discount price if any" step="0.01">

        <label>Stock:</label>
        <input type="number" name="stock" placeholder="Enter quantity in stock" required>

        <label>Category:</label>
        <select name="category_id" required>
            <option value="">-- Select Category --</option>
            <?php 
            mysqli_data_seek($categoriesResult, 0);
            while ($cat = mysqli_fetch_assoc($categoriesResult)): 
            ?>
                <option value="<?= $cat['id'] ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Product Image:</label>
        <input type="file" name="image" accept="image/*">
        <span class="note">Optional. Upload an image for the product.</span>

        <button type="submit">Add Product</button>
    </form>

</div>

</body>
</html>