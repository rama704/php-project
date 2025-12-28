<?php

require_once '../includes/db.connection.php';
require_once 'admin_auth.php';


$db = Database::getInstance();
$conn = $db->getConnection();

if (!isset($_GET['id'])) {
    header("Location: adminproducts.php");
    exit;
}

$product_id = $_GET['id'];
$query = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: adminproducts.php");
    exit;
}

// Fetch categories for dropdown
$categoriesQuery = "SELECT * FROM categories ORDER BY name ASC";
$categoriesResult = mysqli_query($conn, $categoriesQuery);

/* ================== UPDATE PRODUCT ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name        = $_POST['name'];
    $description = $_POST['description'];
    $price       = $_POST['price'];
    $discount    = $_POST['discount_price'];
    $image       = $_POST['image'];
    $category_id = $_POST['category_id'];
    $stock       = $_POST['stock'];

    $query= "UPDATE products 
         SET name = '$name', description = '$description', price = '$price', category_id = '$category_id', discount_price = '$discount', image = '$image', stock = '$stock'
         WHERE id = '$product_id'";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: adminproducts.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="adminproductedit.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- ================= SIDEBAR ================= -->
<aside class="sidebar">
    <div class="sidebar-header">
        <span class="logo-text">Dashboard</span>
        <span class="admin-badge">Admin</span>
    </div>

    <nav class="sidebar-nav">
        <a href="admindashboard.php" class="nav-item">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="adminproducts.php" class="nav-item active">
            <i class="fas fa-box"></i> Products
        </a>
        <a href="categories.php" class="nav-item">
            <i class="fas fa-tags"></i> Categories
        </a>
        <a href="adminorders.php" class="nav-item">
            <i class="fas fa-shopping-cart"></i> Orders
        </a>
        <a href="adminusers.php" class="nav-item">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="reviews.php" class="nav-item">
            <i class="fas fa-star"></i>
            <span>Reviews</span>
            <span class="badge"><?php echo $stats['reviews']; ?></span>
        </a>
         <a href="adminslides.php" class="nav-item">
            <i class="fas fa-star"></i>
            <span>Slides</span>
            <span class="badge"><?php echo $stats['slides']; ?></span>
        </a>
        <a href="logout.php" class="nav-item logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</aside>

<!-- ================= MAIN ================= -->
<main class="main-content">

    <div class="page-header">
        <h1>Edit Product</h1>
        <p>Update product information</p>
    </div>

    <div class="form-container">

        <form method="POST" class="edit-form">

            <label>Product Name</label>
            <input type="text" name="name"
                   value="<?= htmlspecialchars($product['name']) ?>" required>

            <label>Description</label>
            <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea>

            <label>Price</label>
            <input type="number" step="0.01" name="price"
                   value="<?= $product['price'] ?>" required>

            <label>Discount Price</label>
            <input type="number" step="0.01" name="discount_price"
                   value="<?= $product['discount_price'] ?>">

            <label>Image</label>
            <input type="text" name="image"
                   value="<?= htmlspecialchars($product['image']) ?>"
                   placeholder="Enter image filename">

            <label>Stock</label>
            <input type="number" name="stock"
                   value="<?= $product['stock'] ?>" required>

            <label>Category</label>
            <select name="category_id" required>
                <?php 
                mysqli_data_seek($categoriesResult, 0); // Reset pointer
                while ($cat = mysqli_fetch_assoc($categoriesResult)): 
                ?>
                    <option value="<?= $cat['id'] ?>"
                        <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <div class="form-actions">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Update Product
                </button>
                <a href="adminproducts.php" class="btn-cancel">Cancel</a>
            </div>

        </form>

    </div>

</main>

</body>
</html>