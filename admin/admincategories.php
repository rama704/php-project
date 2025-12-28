<?php
// 1. Database Connection
require_once '../includes/db.connection.php';
require_once 'admin_auth.php';

$db = Database::getInstance();
$conn = $db->getConnection();
$category_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM categories"));

$query = "SELECT id, name, description
          FROM categories
          ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Categories</title>
    <link rel="stylesheet" href="admincategories.css">
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
            <a href="adminproducts.php" class="nav-item">
                <i class="fas fa-box"></i>
                <span>Products</span>
            </a>
            <a href="admincategories.php" class="nav-item active">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
                <span class="badge"><?= $category_count ?></span>
            </a>
            <a href="adminorders.php" class="nav-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders</span>
            </a>
            <a href="adminusers.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Users</span>
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
                    <h1>Categories Management</h1>
                    <p>View and manage all available categories in the store</p>
                </div>
                <a href="admincategoriescreate.php" class="add-btn">
                    <i class="fas fa-plus"></i>
                    <span>Add New Category</span>
                </a>
            </div>

            <!-- ================= CATEGORIES TABLE SECTION ================= -->
            <section class="content-section">
                <div class="section-header">
                    <h2>Categories List</h2>
                    <div class="header-actions">
                        <span class="total-count">
                            Total Categories: <?= mysqli_num_rows($result) ?>
                        </span>
                    </div>
                </div>

                <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Category ID</th>
                                <th>Category Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            <?php 
                            mysqli_data_seek($result, 0);
                            while ($row = mysqli_fetch_assoc($result)): 
                            ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($row['id']) ?></strong></td>
                                <td>
                                    <div class="category-name">
                                        <i class="fas fa-tag"></i>
                                        <?= htmlspecialchars($row['name']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="category-description">
                                        <?= htmlspecialchars($row['description']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="view_category.php?id=<?= $row['id'] ?>" class="action-btn view" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="admincategoriesedit.php?id=<?= $row['id'] ?>" class="action-btn edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteCategory(<?= $row['id'] ?>)" class="action-btn delete" title="Delete">
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
                        <i class="fas fa-tags"></i>
                        <h3>No Categories Available</h3>
                        <p>Start by adding new categories to your store</p>
                        <a href="admincategorycreate.php" class="add-btn-small">
                            <i class="fas fa-plus"></i>
                            Add First Category
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

        // Mobile Sidebar Toggle
        if (window.innerWidth <= 992) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }

        // Delete Category Function
        function deleteCategory(categoryId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This category will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `admincategoriesdelete.php?id=${categoryId}`;
                }
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
            window.location.href = '../logout.php';
        }
    });
}
    </script>

</body>
</html>