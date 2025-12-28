<?php

require_once '../includes/db.connection.php';
require_once 'admin_auth.php';



$db = Database::getInstance();
$conn = $db->getConnection();

// Get Dashboard statistics
$stats = [
    'products' => 0,
    'categories' => 0,
    'orders' => 0,
    'users' => 0,
    'reviews' => 0,
    'slides' => 0
];

// Count products
$result = $conn->query("SELECT COUNT(*) as count FROM products");
if($result) $stats['products'] = $result->fetch_assoc()['count'];

// Count categories
$result = $conn->query("SELECT COUNT(*) as count FROM categories");
if($result) $stats['categories'] = $result->fetch_assoc()['count'];

// Count orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
if($result) $stats['orders'] = $result->fetch_assoc()['count'];

// Count users
$result = $conn->query("SELECT COUNT(*) as count FROM users");
if($result) $stats['users'] = $result->fetch_assoc()['count'];

// Count reviews
$result = $conn->query("SELECT COUNT(*) as count FROM reviews");
if($result) $stats['reviews'] = $result->fetch_assoc()['count'];

// Get last 5 orders
$recentOrders = [];
$query = "SELECT o.*, u.full_name 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          ORDER BY o.created_at DESC 
          LIMIT 5";


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Techify</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="admindashboard.css">
</head>
<body>

<!-- ================= SIDEBAR ================= -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2 class="logo-text">Techify</h2>
        <span class="admin-badge">Admin Panel</span>
    </div>

    <nav class="sidebar-nav">
        <a href="admindashboard.php" class="nav-item active">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="adminproducts.php" class="nav-item">
            <i class="fas fa-box"></i>
            <span>Products</span>
            <span class="badge"><?php echo $stats['products']; ?></span>
        </a>
        <a href="admincategories.php" class="nav-item">
            <i class="fas fa-tags"></i>
            <span>Categories</span>
            <span class="badge"><?php echo $stats['categories']; ?></span>
        </a>
        <a href="adminorders.php" class="nav-item">
            <i class="fas fa-shopping-bag"></i>
            <span>Orders</span>
            <span class="badge"><?php echo $stats['orders']; ?></span>
        </a>
        <a href="adminusers.php" class="nav-item">
            <i class="fas fa-users"></i>
            <span>Users</span>
            <span class="badge"><?php echo $stats['users']; ?></span>
        </a>
        <a href="adminreviews.php" class="nav-item">
            <i class="fas fa-star"></i>
            <span>Reviews</span>
            <span class="badge"><?php echo $stats['reviews']; ?></span>
        </a>
         
        <a href="#" onclick="confirmLogout(event)" class="nav-item logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </nav>
</div>

<!-- ================= MAIN CONTENT ================= -->
<div class="main-content">
    
    <!-- Top Bar -->
    <div class="top-bar">
        <button class="toggle-sidebar" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="top-bar-right">
            <div class="admin-profile">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?></span>
                <div class="admin-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-container">
        
        <div class="page-header">
            <h1>Dashboard</h1>
            <p>Overview of Techify Store</p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            
            <div class="stat-card">
                <div class="stat-icon products">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $stats['products']; ?></h3>
                    <p>Total Products</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon categories">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $stats['categories']; ?></h3>
                    <p>Categories</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orders">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $stats['orders']; ?></h3>
                    <p>Orders</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $stats['users']; ?></h3>
                    <p>Users</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon reviews">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $stats['reviews']; ?></h3>
                    <p>Reviews</p>
                </div>
            </div>
            

        </div>

        <!-- Recent Orders -->
        <div class="content-section">
            <div class="section-header">
                <h2>Recent Orders</h2>
                <a href="orders.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($recentOrders)): ?>
                            <?php foreach($recentOrders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['full_name'] ?? 'Guest'); ?></td>
                                    <td>£<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <button class="action-btn view" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px;">
                                    No orders available
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
}

// Activate current page link
const currentPage = window.location.pathname.split('/').pop();
document.querySelectorAll('.nav-item').forEach(item => {
    if(item.getAttribute('href') === currentPage) {
        document.querySelector('.nav-item.active')?.classList.remove('active');
        item.classList.add('active');
    }
});
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