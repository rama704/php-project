<?php



// تضمين اتصال قاعدة البيانات
require_once '../includes/db.connection.php';
$db = Database::getInstance();
$conn = $db->getConnection();

// استعلام جلب الطلبات مع أسماء المستخدمين
$sql = "
    SELECT o.*, u.name AS username
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
";
$result = $conn->query($sql);

// حساب عدد الطلبات
$order_count = $result ? $result->num_rows : 0;

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Orders</title>
    <link rel="stylesheet" href="admincategories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ================= ADDITIONAL ORDERS STYLES ================= */
        
        /* Order ID Styling */
        .order-id {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 15px;
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info i {
            color: var(--accent-color);
            font-size: 16px;
        }

        .user-name {
            font-weight: 600;
            color: var(--primary-color);
        }

        /* Total Price */
        .order-price {
            font-size: 18px;
            font-weight: 800;
            color: var(--accent-color);
        }

        /* Status Badges */
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

        /* Pending - Gray */
        .status-badge.pending {
            background: rgba(158, 158, 158, 0.15);
            color: #9e9e9e;
            border-color: rgba(158, 158, 158, 0.3);
        }

        /* Paid - Blue */
        .status-badge.paid {
            background: rgba(33, 150, 243, 0.15);
            color: #2196f3;
            border-color: rgba(33, 150, 243, 0.3);
        }

        /* Shipped - Orange */
        .status-badge.shipped {
            background: rgba(255, 152, 0, 0.15);
            color: #ff9800;
            border-color: rgba(255, 152, 0, 0.3);
        }

        /* Completed - Green */
        .status-badge.completed {
            background: rgba(76, 175, 80, 0.15);
            color: #4caf50;
            border-color: rgba(76, 175, 80, 0.3);
        }

        /* Canceled - Red */
        .status-badge.canceled {
            background: rgba(244, 67, 54, 0.15);
            color: #f44336;
            border-color: rgba(244, 67, 54, 0.3);
        }

        /* Order Date */
        .order-date {
            font-size: 14px;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .order-date i {
            color: var(--accent-color);
        }

        /* View Details Button */
        .btn-view-details {
            padding: 8px 20px;
            background: linear-gradient(135deg, var(--accent-color), var(--gold-color));
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-view-details:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(254, 164, 11, 0.4);
        }

        /* Badge for sidebar */
        .badge {
            background: var(--accent-color);
            color: var(--white);
            padding: 3px 8px;
            border-radius: 50%;
            font-size: 11px;
            font-weight: 700;
            margin-left: auto;
        }

        /* Table Enhancements */
        .data-table tbody tr {
            transition: all 0.3s ease;
        }

        .data-table tbody tr:hover {
            background: rgba(254, 164, 11, 0.05);
            transform: translateX(5px);
        }

        /* Empty State Enhancement */
        .empty-state {
            background: var(--white);
            border-radius: 15px;
            padding: 80px 40px;
            text-align: center;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
        }

        .empty-state-content i {
            font-size: 80px;
            color: var(--accent-color);
            margin-bottom: 25px;
            opacity: 0.3;
        }

        .empty-state-content h3 {
            font-size: 28px;
            color: var(--primary-color);
            margin-bottom: 15px;
            font-weight: 800;
        }

        .empty-state-content p {
            font-size: 16px;
            color: var(--text-light);
            margin-bottom: 30px;
        }

        /* Responsive Table */
        @media (max-width: 768px) {
            .data-table thead {
                display: none;
            }

            .data-table tbody tr {
                display: block;
                margin-bottom: 25px;
                padding: 20px;
                border: 2px solid rgba(254, 164, 11, 0.1);
                border-radius: 15px;
                background: var(--white);
            }

            .data-table tbody td {
                display: block;
                padding: 10px 0;
                text-align: left;
                border: none;
            }

            .data-table tbody td::before {
                content: attr(data-label);
                font-weight: 700;
                color: var(--primary-color);
                display: block;
                margin-bottom: 8px;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .btn-view-details {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
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
            <a href="admincategories.php" class="nav-item">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
            </a>
            <a href="orders.php" class="nav-item active">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders</span>
                <span class="badge"><?= $order_count ?></span>
            </a>
            <a href="adminusers.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            
            <a href="login.php" class="nav-item logout">
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
                    <h1>Orders Management</h1>
                    <p>View and manage all customer orders</p>
                </div>
            </div>

            <!-- ================= ORDERS TABLE SECTION ================= -->
            <section class="content-section">
                <div class="section-header">
                    <h2>Orders List</h2>
                    <div class="header-actions">
                        <span class="total-count">
                            Total Orders: <?= $order_count ?>
                        </span>
                    </div>
                </div>

                <?php if ($order_count > 0): ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>User</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td data-label="Order ID">
                                    <span class="order-id">#<?= htmlspecialchars($row['id']) ?></span>
                                </td>
                                
                                <td data-label="User">
                                    <div class="user-info">
                                        <i class="fas fa-user-circle"></i>
                                        <span class="user-name">
                                            <?= htmlspecialchars($row['username'] ?? 'Guest User') ?>
                                        </span>
                                    </div>
                                </td>
                                
                                <td data-label="Total Price">
                                    <span class="order-price">
                                        £<?= number_format($row['total_price'], 2) ?>
                                    </span>
                                </td>
                                
                                <td data-label="Status">
                                    <?php
                                    $status_icons = [
                                        'pending' => 'clock',
                                        'paid' => 'check-circle',
                                        'shipped' => 'truck',
                                        'completed' => 'check-double',
                                        'canceled' => 'times-circle'
                                    ];
                                    $status = strtolower($row['status'] ?? 'pending');
                                    $icon = $status_icons[$status] ?? 'info-circle';
                                    ?>
                                    <span class="status-badge <?= $status ?>">
                                        <i class="fas fa-<?= $icon ?>"></i>
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                                
                                <td data-label="Order Date">
                                    <div class="order-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span><?= date('d M Y', strtotime($row['created_at'])) ?></span>
                                    </div>
                                </td>
                                
                                <td data-label="Actions">
                                    <a href="order_items.php?id=<?= $row['id'] ?>" class="btn-view-details">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-content">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>No Orders Available</h3>
                        <p>Orders from customers will appear here</p>
                    </div>
                </div>
                <?php endif; ?>

            </section>

        </div>
    </main>

    

    <script>
        // Toggle Sidebar
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
    </script>

</body>
</html>