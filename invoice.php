<?php
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: cart.php");
    exit;
}

$order_id = (int)$_GET['id'];

require_once __DIR__ . '/includes/db.connection.php';
$db = Database::getInstance();
$conn = $db->getConnection();

// جلب تفاصيل الطلب
$stmt = $conn->prepare("
    SELECT o.*, u.name AS username, u.email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: cart.php");
    exit;
}

// جلب عناصر الطلب
$stmt = $conn->prepare("
    SELECT oi.*, p.name, p.image
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$orderItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// جلب بيانات الدفع
$stmt = $conn->prepare("
    SELECT *
    FROM fake_payment_details
    WHERE order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Invoice - Techify</title>
    <link rel="stylesheet" href="products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .invoice-section {
            padding: 60px 0 80px;
            background: var(--bg-light);
        }

        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            border: 2px solid rgba(254,164,11,0.1);
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(254,164,11,0.1);
        }

        .invoice-header h1 {
            font-size: 36px;
            color: var(--primary-color);
            margin-bottom: 10px;
            font-weight: 800;
        }

        .invoice-header p {
            font-size: 16px;
            color: var(--text-light);
            margin: 0;
        }

        .invoice-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        .info-box {
            background: linear-gradient(135deg, #f9f9f9, #fafafa);
            padding: 20px;
            border-radius: 10px;
            border: 2px solid rgba(254,164,11,0.05);
        }

        .info-box h3 {
            font-size: 18px;
            color: var(--primary-color);
            margin-bottom: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-box p {
            margin: 5px 0;
            font-size: 14px;
            color: var(--primary-color);
        }

        .info-box p strong {
            color: var(--accent-color);
        }

        .order-items {
            margin-bottom: 40px;
        }

        .order-item {
            display: flex;
            gap: 15px;
            padding: 15px;
            background: linear-gradient(135deg, #f9f9f9, #fafafa);
            border-radius: 10px;
            margin-bottom: 15px;
            border: 2px solid rgba(254,164,11,0.05);
            transition: all 0.3s ease;
        }

        .order-item:hover {
            border-color: rgba(254,164,11,0.2);
            transform: translateX(5px);
        }

        .order-item-image {
            width: 70px;
            height: 70px;
            background: var(--white);
            border-radius: 8px;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(254,164,11,0.1);
            flex-shrink: 0;
        }

        .order-item-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .order-item-details {
            flex: 1;
        }

        .order-item-details h4 {
            font-size: 14px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
            line-height: 1.3;
        }

        .order-item-details p {
            font-size: 13px;
            color: var(--text-light);
            margin: 3px 0;
        }

        .order-item-price {
            font-size: 16px;
            font-weight: 800;
            color: var(--accent-color);
            text-align: right;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .summary-table th,
        .summary-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-table th {
            background: var(--bg-light);
            color: var(--primary-color);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        .summary-table td {
            color: var(--primary-color);
        }

        .summary-table .total {
            font-size: 20px;
            font-weight: 800;
            color: var(--accent-color);
        }

        .invoice-actions {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            justify-content: center;
        }

        .invoice-actions a {
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .invoice-actions a.btn-primary {
            background: linear-gradient(135deg, var(--accent-color), var(--gold-color));
            color: var(--white);
        }

        .invoice-actions a.btn-secondary {
            background: var(--white);
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .invoice-actions a:hover {
            transform: translateY(-3px);
        }
    </style>
</head>
<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar">
    <div class="container">
        <div class="nav-wrapper">
            <span class="logo-text">Techify</span>
            <ul class="nav-menu">
                <li><a href="index/index.php">HOME</a></li>
                <li><a href="products.php">SHOP</a></li>
                <li><a href="index/index.php#hotdiscounts">HOT DISCOUNTS</a></li>
                <li><a href="index/index.php#features">FEATURES</a></li>
                <li><a href="index/index.php#contact">CONTACT US</a></li>
            </ul>
            <div class="nav-icons">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="profile/profile1.php">Profile</a>
                    <a href="logout.php" class="icon-btn"><i class="fas fa-sign-out-alt"></i></a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
                <a href="cart.php" class="icon-btn cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge">0</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- ================= INVOICE SECTION ================= -->
<section class="invoice-section">
    <div class="container">
        <div class="invoice-container">
            <div class="invoice-header">
                <h1><i class="fas fa-receipt"></i> Order Invoice</h1>
                <p>Thank you for your purchase! Your order has been successfully placed.</p>
            </div>

            <div class="invoice-info">
                <div class="info-box">
                    <h3><i class="fas fa-user"></i> Customer Info</h3>
                    <p><strong>Name:</strong> <?= htmlspecialchars($order['username']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                </div>

                <div class="info-box">
                    <h3><i class="fas fa-clock"></i> Order Info</h3>
                    <p><strong>Order ID:</strong> #<?= $order['id'] ?></p>
                    <p><strong>Date:</strong> <?= date('F j, Y, g:i A', strtotime($order['created_at'])) ?></p>
                    <p><strong>Status:</strong> <span style="color: #4caf50; font-weight: bold;">Completed</span></p>
                </div>
            </div>

            <h3><i class="fas fa-shopping-bag"></i> Order Items</h3>
            <div class="order-items">
                <?php foreach($orderItems as $item): ?>
                    <div class="order-item">
                        <div class="order-item-image">
                            <?php if(!empty($item['image'])): ?>
                                <img src="images/<?= htmlspecialchars($item['image']) ?>" 
                                     alt="<?= htmlspecialchars($item['name']) ?>">
                            <?php else: ?>
                                <i class="fas fa-box"></i>
                            <?php endif; ?>
                        </div>
                        <div class="order-item-details">
                            <h4><?= htmlspecialchars($item['name']) ?></h4>
                            <p>Qty: <?= $item['quantity'] ?> × £<?= number_format($item['price'], 2) ?></p>
                        </div>
                        <div class="order-item-price">
                            £<?= number_format($item['quantity'] * $item['price'], 2) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <h3><i class="fas fa-money-bill-wave"></i> Payment Summary</h3>
            <table class="summary-table">
                <tr>
                    <th>Subtotal</th>
                    <td>£<?= number_format($order['total_price'] / 1.15, 2) ?></td>
                </tr>
                <tr>
                    <th>Tax (15%)</th>
                    <td>£<?= number_format($order['total_price'] - ($order['total_price'] / 1.15), 2) ?></td>
                </tr>
                <tr>
                    <th>Shipping</th>
                    <td>£0.00</td>
                </tr>
                <tr>
                    <th class="total">Total</th>
                    <td class="total">£<?= number_format($order['total_price'], 2) ?></td>
                </tr>
            </table>

            <h3><i class="fas fa-credit-card"></i> Payment Method</h3>
            <div class="info-box">
                <p><strong>Cardholder:</strong> <?= htmlspecialchars($payment['cardholder_name']) ?></p>
                <p><strong>Card Number:</strong> <?= htmlspecialchars($payment['card_number']) ?></p>
                <p><strong>Expiry:</strong> <?= htmlspecialchars($payment['expiry_date']) ?></p>
                <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($payment['address'])) ?></p>
            </div>

            <div class="invoice-actions">
                <a href="products.php" class="btn btn-secondary">
                    <i class="fas fa-shopping-cart"></i> Continue Shopping
                </a>
                <a href="profile/profile1.php" class="btn btn-primary">
                    <i class="fas fa-user"></i> View My Orders
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h3>Techify</h3>
                <p>Your trusted electronics store for laptops, headphones and the latest technology.</p>
            </div>

            <div class="footer-col">
                <h3>Contact Us</h3>
                <ul class="footer-list">
                    <li>📍 Amman, Jordan</li>
                    <li>📞 +962 7 0000 0000</li>
                    <li>✉️ support@techify.com</li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul class="footer-list">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Shop</a></li>
                    <li><a href="index.php#hotdiscounts">Hot Discount</a></li>
                    <li><a href="index.php#features">Features</a></li>
                    <li><a href="index.php#contact">Contact</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

</body>
</html>