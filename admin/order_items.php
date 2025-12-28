<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

$order_id = (int) $_GET['id'];

require_once '../includes/db.connection.php';
$db = Database::getInstance();
$conn = $db->getConnection();

// جلب تفاصيل الطلب
$stmt = $conn->prepare("
    SELECT o.*, u.name AS username
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: orders.php");
    exit;
}

// جلب عناصر الطلب
$stmt = $conn->prepare("
    SELECT oi.*, p.name AS product_name, p.image
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$orderItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Admin</title>
    <link rel="stylesheet" href="admincategories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .order-details-section {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .order-id-large {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary-color);
        }

        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .order-info-item {
            display: flex;
            flex-direction: column;
        }

        .order-info-label {
            font-size: 12px;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .order-info-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 2px solid;
        }

        .status-badge.completed {
            background: rgba(76, 175, 80, 0.15);
            color: #4caf50;
            border-color: rgba(76, 175, 80, 0.3);
        }

        .status-badge.pending {
            background: rgba(158, 158, 158, 0.15);
            color: #9e9e9e;
            border-color: rgba(158, 158, 158, 0.3);
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .products-table th,
        .products-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .products-table th {
            background: #f8f9fa;
            color: var(--primary-color);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: var(--primary-color);
            transform: translateX(-5px);
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span class="logo-text">Dashboard</span>
            <span class="admin-badge">Admin</span>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="adminproducts.php" class="nav-item">
                <i class="fas fa-box"></i>
                <span>Products</span>
            </a>
            <a href="admincategories.php" class="nav-item">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
            </a>
            <a href="orders.php" class="nav-item active">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders</span>
            </a>
            <a href="users.php" class="nav-item">
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

    <!-- MAIN CONTENT -->
    <main class="main-content" id="mainContent">
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

        <div class="dashboard-container">
            <a href="adminorders.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to Orders
            </a>

            <div class="page-header">
                <h1>Order #<?= $order['id'] ?> Details</h1>
                <p>View order items and customer information</p>
            </div>

            <!-- Order Summary -->
            <div class="order-details-section">
                <div class="order-header">
                    <div class="order-id-large">#<?= $order['id'] ?></div>
                    <span class="status-badge <?= strtolower($order['status']) ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>

                <div class="order-info-grid">
                    <div class="order-info-item">
                        <span class="order-info-label">Customer</span>
                        <span class="order-info-value"><?= htmlspecialchars($order['username'] ?? 'Guest') ?></span>
                    </div>
                    <div class="order-info-item">
                        <span class="order-info-label">Order Date</span>
                        <span class="order-info-value"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></span>
                    </div>
                    <div class="order-info-item">
                        <span class="order-info-label">Total Amount</span>
                        <span class="order-info-value">£<?= number_format($order['total_price'], 2) ?></span>
                    </div>
                </div>
            </div>

            <!-- Update Status Form -->
            <div class="order-details-section">
                <h3>Update Order Status</h3>
                <form method="POST" action="update_order_status.php">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <div style="display: flex; gap: 15px; margin-top: 15px;">
                        <select name="status">
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending
                            </option>
                            <option value="paid" <?= $order['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped
                            </option>
                            <option value="failed" <?= $order['status'] === 'failed' ? 'selected' : '' ?>>Failed</option>
                        </select>
                        <button type="submit"
                            style="padding: 10px 20px; background: linear-gradient(135deg, var(--accent-color), var(--gold-color)); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>

            <!-- Order Items -->
            <section class="content-section">
                <div class="section-header">
                    <h2>Order Items</h2>
                </div>

                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderItems as $item): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="../images/<?= htmlspecialchars($item['image']) ?>"
                                                alt="<?= htmlspecialchars($item['product_name']) ?>" class="product-image">
                                        <?php else: ?>
                                            <i class="fas fa-box" style="font-size: 24px; color: #aaa;"></i>
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars($item['product_name']) ?></span>
                                    </div>
                                </td>
                                <td>£<?= number_format($item['price'], 2) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>£<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebar');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        if (window.innerWidth <= 992) {
            toggleBtn.addEventListener('click', () => {
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
            window.location.href = 'login.php';
        }
    });
}
    </script>

</body>

</html>