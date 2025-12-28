<?php
session_start();
require_once '../includes/db.connection.php';

$db = Database::getInstance();
$conn = $db->getConnection();

if (!isset($_GET['id'])) {
    header("Location:admincategories.php");
    exit;
}

$category_id = $_GET['id'];
$query = "SELECT * FROM categories WHERE id = $category_id";
$result = mysqli_query($conn, $query);
$category = mysqli_fetch_assoc($result);

if (!$category) {
    header("Location: admincategories.php");
    exit;
}

// Fetch categories for dropdown
$categoriesQuery = "SELECT * FROM categories ORDER BY name ASC";
$categoriesResult = mysqli_query($conn, $categoriesQuery);

/* ================== UPDATE CATEGORY ================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name        = $_POST['name'];
    $description = $_POST['description'];
    $query= "UPDATE categories 
         SET name = '$name', description = '$description'
         WHERE id = '$category_id'";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: admincategories.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
    <link rel="stylesheet" href="admincategoriesedit.css">
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
        <a href="admincategories.php" class="nav-item">
            <i class="fas fa-tags"></i> Categories
        </a>
        <a href="adminorders.php" class="nav-item">
            <i class="fas fa-shopping-cart"></i> Orders
            <span class="badge"><?= $order_count ?></span>
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
    </nav>
</aside>

<!-- ================= MAIN ================= -->
<main class="main-content">

    <div class="page-header">
        <h1>Edit Category</h1>
        <p>Update category information</p>
    </div>

    <div class="form-container">

        <form method="POST" class="edit-form">

            <label>Category Name</label>
            <input type="text" name="name"
                   value="<?= htmlspecialchars($category['name']) ?>" required>

            <label>Description</label>
            <textarea name="description" required><?= htmlspecialchars($category['description']) ?></textarea>


            <div class="form-actions">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Update Category
                </button>
                <a href="admincategories.php" class="btn-cancel">Cancel</a>
            </div>

        </form>

    </div>

</main>

</body>
</html>