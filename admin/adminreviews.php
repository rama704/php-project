<?php
require_once 'admin_auth.php';
require_once("../includes/db.connection.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

$sql = "SELECT 
            r.id, r.rating, r.comment, r.created_at,
            u.name AS username, u.email,
            p.name AS product_name, p.image, p.price
        FROM reviews r
        LEFT JOIN users u ON r.user_id = u.id
        LEFT JOIN products p ON r.product_id = p.id
        ORDER BY r.created_at DESC";

$result = $conn->query($sql);
$reviews = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews List - Admin</title>
    <link rel="stylesheet" href="admincategories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .review-card {
            background: var(--white);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }

        .review-card:hover {
            transform: translateY(-3px);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .review-id-large {
            font-size: 24px;
            font-weight: 800;
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

        .status-badge.approved {
            background: rgba(76, 175, 80, 0.15);
            color: #4caf50;
            border-color: rgba(76, 175, 80, 0.3);
        }

        .status-badge.pending {
            background: rgba(254, 164, 11, 0.15);
            color: var(--accent-color);
            border-color: rgba(254, 164, 11, 0.3);
        }

        .status-badge.rejected {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
            border-color: rgba(220, 53, 69, 0.3);
        }

        .rating-stars {
            display: flex;
            gap: 5px;
            font-size: 20px;
            margin: 10px 0;
        }

        .rating-stars .star {
            color: #ddd;
        }

        .rating-stars .star.filled {
            color: var(--accent-color);
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 15px 0;
            padding: 15px;
            background: var(--bg-light);
            border-radius: 8px;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        .product-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .product-price {
            font-size: 14px;
            color: var(--text-light);
        }

        .review-comment {
            background: var(--bg-light);
            border-left: 4px solid var(--accent-color);
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.6;
            color: #555;
            margin: 15px 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 15px 0;
            padding: 15px;
            background: var(--bg-light);
            border-radius: 8px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-color), var(--gold-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 24px;
            font-weight: 800;
        }

        .user-details h4 {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0 0 5px 0;
        }

        .user-details p {
            font-size: 13px;
            color: var(--text-light);
            margin: 0;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-action {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-approve {
            background: #28a745;
            color: white;
        }

        .btn-approve:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-reject {
            background: #dc3545;
            color: white;
        }

        .btn-reject:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: var(--primary-color);
            color: white;
        }

        .btn-delete:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
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

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            color: var(--primary-color);
        }

        .page-header p {
            color: var(--text-light);
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
            <a href="adminorders.php" class="nav-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders</span>
            </a>

            <a href="adminusers.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="adminreviews.php" class="nav-item active">
                <i class="fas fa-star"></i>
                <span>Reviews</span>
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
            <a href="reviews.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to Reviews
            </a>

            <div class="page-header">
                <h1>All Reviews</h1>
                <p>Manage and moderate customer reviews</p>
            </div>

            <?php if (empty($reviews)): ?>
                <div style="text-align: center; padding: 40px; background: var(--bg-light); border-radius: 15px;">
                    <i class="fas fa-inbox" style="font-size: 48px; color: #aaa; margin-bottom: 15px;"></i>
                    <h3>No reviews found.</h3>
                    <p>There are currently no reviews to display.</p>
                </div>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-id-large">#<?= $review['id'] ?></div>

                            <span class="status-badge approved">
                                <i class="fas fa-check-circle"></i>
                                Approved
                            </span>
                        </div>

                        <!-- User Info -->
                        <div class="user-info">
                            <div class="user-avatar">
                                <?= strtoupper(substr($review['username'] ?? 'U', 0, 1)) ?>
                            </div>
                            <div class="user-details">
                                <h4><?= htmlspecialchars($review['username'] ?? 'Unknown User') ?></h4>
                                <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($review['email'] ?? '—') ?></p>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="product-info">
                            <?php if (!empty($review['image'])): ?>
                                <img src="../images/<?= htmlspecialchars($review['image']) ?>"
                                    alt="<?= htmlspecialchars($review['product_name'] ?? 'Product') ?>" class="product-image">
                            <?php else: ?>
                                <i class="fas fa-box" style="font-size: 32px; color: #aaa;"></i>
                            <?php endif; ?>
                            <div>
                                <div class="product-name"><?= htmlspecialchars($review['product_name'] ?? '—') ?></div>
                                <?php if (isset($review['price'])): ?>
                                    <div class="product-price">£<?= number_format((float) $review['price'], 2) ?></div>
                                <?php else: ?>
                                    <div class="product-price">—</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Rating -->
                        <div class="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?= $i <= $review['rating'] ? 'filled' : '' ?>">★</span>
                            <?php endfor; ?>
                        </div>

                        <!-- Comment -->
                        <div class="review-comment">
                            <?= htmlspecialchars($review['comment'] ?? 'No comment provided.') ?>
                        </div>


                        <div class="action-buttons">
    <button class="btn-action btn-delete" onclick="deleteReview(<?= $review['id'] ?>)">
        <i class="fas fa-trash"></i> Delete Review
    </button>
</div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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

        // Update Review Status
        function updateReviewStatus(id, status) {
            const action = status === 'approved' ? 'approve' : 'reject';
            Swal.fire({
                title: action.charAt(0).toUpperCase() + action.slice(1) + ' Review?',
                text: 'Do you want to ' + action + ' this review?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: status === 'approved' ? '#28a745' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, ' + action + ' it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('update_review_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'review_id=' + id + '&status=' + status
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(
                                    'Success!',
                                    'Review has been ' + status + '.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', data.message || 'Something went wrong.', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'Failed to update review status.', 'error');
                        });
                }
            });
        }

        // Delete Review
        function deleteReview(id) {
            Swal.fire({
                title: 'Delete Review?',
                text: 'This action cannot be undone!',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete_review.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'review_id=' + id
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'Review has been deleted.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', data.message || 'Something went wrong.', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'Failed to delete review.', 'error');
                        });
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