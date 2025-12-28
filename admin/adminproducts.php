<?php
// 1. Database Connection
require_once '../includes/db.connection.php';
require_once 'admin_auth.php';

$db = Database::getInstance();
$conn = $db->getConnection();
$query = "SELECT products.id, products.name, products.price, products.discount_price, products.stock, products.image, categories.name as category_name 
          FROM products 
          LEFT JOIN categories ON products.category_id = categories.id
          ORDER BY products.id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Products</title>
    <link rel="stylesheet" href="adminproducts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <!-- ================= SIDEBAR ================= -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span class="logo-text">Dashboard</span>
            <span class="admin-badge">Admin</span>
        </div>

        <nav class="sidebar-nav">
            <a href="admindashboard.php" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="adminproducts.php" class="nav-item active">
                <i class="fas fa-box"></i>
                <span>Products</span>
                <span class="badge"><?= mysqli_num_rows($result) ?></span>
            </a>
            <a href="admincategories.php" class="nav-item">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
            </a>
            <a href="adminorders.php" class="nav-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders</span>
            </a>
            <a href="adminusers.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="adminreviews.php" class="nav-item">
                <i class="fas fa-star"></i>
                <span>Reviews</span>
            </a>
            <a href="adminslides.php" class="nav-item">
                <i class="fas fa-star"></i>
                <span>Slides</span>
            </a>
            <a href="#" onclick="confirmLogout(event)" class="nav-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-content" id="mainContent">

        <!-- ================= TOP BAR ================= -->
        <div class="top-bar">
            <button class="toggle-sidebar" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>

            <div class="top-bar-right">
                <div class="admin-profile">
                    <span>Welcome, Admin</span>
                    <div class="admin-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= DASHBOARD CONTAINER ================= -->
        <div class="dashboard-container">

            <!-- ================= PAGE HEADER ================= -->
            <div class="page-header">
                <div>
                    <h1>Products Management</h1>
                    <p>View and manage all available products in the store</p>
                </div>
                <a href="adminproductcreate.php" class="add-btn">
                    <i class="fas fa-plus"></i>
                    <span>Add New Product</span>
                </a>
            </div>

            <!-- ================= PRODUCTS TABLE SECTION ================= -->
            <section class="content-section">
                <div class="section-header">
                    <h2>Products List</h2>
                    <div class="header-actions">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search for a product..." id="searchInput">
                        </div>
                    </div>
                </div>

                <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Original Price</th>
                                    <th>Discount</th>
                                    <th>Final Price</th>
                                    <th>Stock</th>
                                    <th>Category</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                <?php
                                mysqli_data_seek($result, 0); // Reset pointer to start
                                while ($row = mysqli_fetch_assoc($result)):
                                    $discount = $row['discount_price'];
                                    $finalPrice = $discount > 0 ? $row['price'] - $discount : $row['price'];
                                    $discountPercent = $discount > 0 ? round(($discount / $row['price']) * 100) : 0;

                                    // Determine stock status
                                    $stockClass = 'out-of-stock';
                                    $stockText = 'Out of Stock';
                                    if ($row['stock'] > 10) {
                                        $stockClass = 'in-stock';
                                        $stockText = 'In Stock';
                                    } elseif ($row['stock'] > 0) {
                                        $stockClass = 'low-stock';
                                        $stockText = 'Low Stock';
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="product-image">
                                                <?php if (!empty($row['image'])): ?>
                                                    <img src="../uploads/products/<?= htmlspecialchars($row['image']) ?>"
                                                        alt="<?= htmlspecialchars($row['name']) ?>">
                                                <?php else: ?>
                                                    <div class="no-image">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><strong>#<?= $row['id'] ?></strong></td>
                                        <td>
                                            <div class="product-name"><?= htmlspecialchars($row['name']) ?></div>
                                        </td>
                                        <td>
                                            <span class="price-tag">$<?= number_format($row['price'], 2) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($discount > 0): ?>
                                                <span class="discount-badge"><?= $discountPercent ?>% OFF</span>
                                            <?php else: ?>
                                                <span class="no-discount">No Discount</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="final-price">$<?= number_format($finalPrice, 2) ?></span>
                                        </td>
                                        <td>
                                            <span class="stock-badge <?= $stockClass ?>">
                                                <?= $stockText ?> (<?= $row['stock'] ?>)
                                            </span>
                                        </td>
                                        <td>
                                            <span class="category-badge">
                                                <i class="fas fa-tag"></i>
                                                <?= htmlspecialchars($row['category_name']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="view_product.php?id=<?= $row['id'] ?>" class="action-btn view"
                                                    title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="adminproductedit.php?id=<?= $row['id'] ?>" class="action-btn edit"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="deleteProduct(<?= $row['id'] ?>)" class="action-btn delete"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-content">
                            <i class="fas fa-box-open"></i>
                            <h3>No Products Available</h3>
                            <p>Start by adding new products to your store</p>
                            <a href="add_product.php" class="add-btn-small">
                                <i class="fas fa-plus"></i>
                                Add First Product
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </section>

        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Toggle Sidebar
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebar');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Search Functionality
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('productsTableBody');

        if (searchInput && tableBody) {
            searchInput.addEventListener('keyup', function () {
                const filter = this.value.toLowerCase();
                const rows = tableBody.getElementsByTagName('tr');

                Array.from(rows).forEach(row => {
                    const productName = row.querySelector('.product-name');
                    const categoryName = row.querySelector('.category-badge');

                    if (productName || categoryName) {
                        const nameText = productName ? productName.textContent.toLowerCase() : '';
                        const catText = categoryName ? categoryName.textContent.toLowerCase() : '';

                        if (nameText.includes(filter) || catText.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
        }

        // Delete Product Function
        function deleteProduct(productId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This product will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `adminproductdelete.php?id=${productId}`;
                }
            });
        }
        <?php if (isset($_GET['deleted'])): ?>
                < script >
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Product has been deleted successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });
        </script>

    <?php endif; ?>

    // Mobile Sidebar Toggle
    if (window.innerWidth <= 992) { toggleBtn.addEventListener('click', ()=> {
        sidebar.classList.toggle('active');
        });
        }

        /* Logout Confirmation */
        function confirmLogout(event) {
        event.preventDefault();

        Swal.fire({
        title: 'Logout?',
        text: 'Are you sure you want to logout?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'Cancel'
        }).then((result) => {
        if (result.isConfirmed) {
        window.location.href = 'logout.php';
        }
        });
        }
        </script>
</body>

</html>