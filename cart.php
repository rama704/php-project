<?php
session_start();
require_once __DIR__ . '/includes/db.connection.php';

$db = Database::getInstance();
$conn = $db->getConnection();

// Get user ID or session ID
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$session_id = session_id();

// Fetch cart items based on user_id or session_id
if ($user_id) {
    $sql = "SELECT c.*, p.name, p.price, p.image
FROM cart c
JOIN products p ON c.product_id = p.id
WHERE c.user_id = ?
ORDER BY c.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
} else {
    $sql = "SELECT c.*, p.name, p.price, p.image
FROM cart c
JOIN products p ON c.product_id = p.id
WHERE c.session_id = ?
ORDER BY c.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_id);
}
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

// Calculate totals
$cart_total = 0;
$cart_quantity = 0;
foreach ($cart_items as $item) {
    $item_total = $item['price'] * $item['quantity'];
    $cart_total += $item_total;
    $cart_quantity += $item['quantity'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Techify</title>
    <style>
        /* Include your existing CSS styles here (navbar, footer, etc.) */
        /* You can copy the styles from product_details.php or link to a shared CSS file */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #0b0b0b;
            --secondary-color: #292929;
            --accent-color: #fea40b;
            --gold-color: #84641c;
            --white: #ffffff;
            --text-light: #999;
            --bg-light: #f8f8f8;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-light);
            color: var(--primary-color);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ================= NAVBAR ================= */
        .navbar {
            background: var(--white);
            padding: 25px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .navbar:hover {
            box-shadow: 0 4px 25px rgba(254, 164, 11, 0.1);
        }

        .nav-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-text {
            color: var(--primary-color);
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .logo-text:hover {
            transform: scale(1.05);
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 45px;
        }

        .nav-menu a {
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            position: relative;
            padding: 8px 0;
        }

        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent-color);
            transition: width 0.3s ease;
        }

        .nav-menu a:hover::after,
        .nav-menu a.active::after {
            width: 100%;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            color: var(--accent-color);
        }

        .nav-icons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .icon-btn {
            background: transparent;
            border: 2px solid transparent;
            cursor: pointer;
            color: var(--primary-color);
            font-size: 18px;
            padding: 0;
            border-radius: 50%;
            transition: all 0.3s ease;
            position: relative;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-btn:hover {
            color: var(--accent-color);
            background: rgba(254, 164, 11, 0.1);
            border-color: var(--accent-color);
            transform: translateY(-2px);
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accent-color);
            color: var(--white);
            font-size: 11px;
            font-weight: 700;
            padding: 3px 7px;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(254, 164, 11, 0.4);
        }

        .nav-icons a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: transparent;
            border: 2px solid var(--primary-color);
            border-radius: 25px;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .nav-icons a:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(11, 11, 11, 0.2);
        }

        /* ================= PAGE HEADER ================= */
        .page-header {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #1a1a1a 100%);
            padding: 80px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23fea40b" fill-opacity="0.05" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
        }

        .page-header h1 {
            font-size: 48px;
            color: var(--white);
            font-weight: 800;
            margin-bottom: 15px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }

        .page-header p {
            font-size: 18px;
            color: var(--accent-color);
            font-weight: 500;
            position: relative;
            z-index: 1;
        }

        /* ================= CONTAINER ================= */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        /* ================= BACK BUTTON ================= */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            margin-bottom: 30px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 10px 20px;
            border-radius: 25px;
            border: 2px solid transparent;
        }

        .back-button:hover {
            color: var(--accent-color);
            background: rgba(254, 164, 11, 0.1);
            border-color: var(--accent-color);
            transform: translateX(-5px);
        }

        /* ================= CART CARD ================= */
        .cart-card {
            background: var(--white);
            border-radius: 15px;
            padding: 50px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            border: 2px solid rgba(254, 164, 11, 0.1);
            position: relative;
            overflow: hidden;
        }

        .cart-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(254, 164, 11, 0.03) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ================= CART TABLE ================= */
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .cart-table th,
        .cart-table td {
            padding: 20px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        .cart-table th {
            background: var(--bg-light);
            color: var(--primary-color);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        .cart-item-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .cart-item-details {
            display: flex;
            flex-direction: column;
        }

        .cart-item-name {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .cart-item-price {
            color: var(--accent-color);
            font-weight: 700;
            font-size: 16px;
        }

        .cart-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #ddd;
            background: var(--white);
            color: var(--primary-color);
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover {
            background: var(--primary-color);
            color: var(--white);
            border-color: var(--primary-color);
        }

        .quantity-input {
            width: 50px;
            height: 30px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .cart-subtotal {
            font-weight: 700;
            color: var(--primary-color);
        }

        .cart-remove {
            background: none;
            border: none;
            color: #e74c3c;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .cart-remove:hover {
            color: var(--primary-color);
            transform: scale(1.1);
        }

        /* ================= CART SUMMARY ================= */
        .cart-summary {
            background: var(--bg-light);
            border-radius: 10px;
            padding: 30px;
            margin-top: 40px;
        }

        .cart-summary h3 {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 25px;
            font-weight: 700;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .summary-label {
            color: var(--text-light);
        }

        .summary-value {
            font-weight: 700;
            color: var(--primary-color);
        }

        .cart-total {
            border-top: 2px solid var(--accent-color);
            padding-top: 15px;
            margin-top: 15px;
            font-size: 20px;
        }

        .cart-total .summary-value {
            color: var(--accent-color);
            font-size: 24px;
        }

        .cart-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 30px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
            text-decoration: none;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transition: width 0.6s, height 0.6s, top 0.6s, left 0.6s;
            transform: translate(-50%, -50%);
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-color), var(--gold-color));
            color: var(--white);
            box-shadow: 0 4px 20px rgba(254, 164, 11, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(254, 164, 11, 0.5);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: translateY(-3px);
        }

        /* ================= EMPTY CART ================= */
        .empty-cart {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-cart i {
            font-size: 80px;
            color: var(--accent-color);
            margin-bottom: 25px;
            opacity: 0.5;
        }

        .empty-cart h3 {
            font-size: 28px;
            color: var(--primary-color);
            margin-bottom: 15px;
            font-weight: 800;
        }

        .empty-cart p {
            font-size: 16px;
            color: var(--text-light);
            margin-bottom: 30px;
        }

        /* ================= FOOTER ================= */
        .footer {
            background: linear-gradient(135deg, #0a0a0a, var(--primary-color));
            color: var(--white);
            padding: 60px 0 30px;
            margin-top: 80px;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 50px;
        }

        .footer-section h3 {
            color: var(--accent-color);
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 700;
        }

        .footer-section p {
            color: #ccc;
            line-height: 1.8;
            font-size: 14px;
        }

        .footer-section a {
            color: #ccc;
            text-decoration: none;
            display: block;
            margin-bottom: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .footer-section a:hover {
            color: var(--accent-color);
            transform: translateX(5px);
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 992px) {
            .nav-menu {
                display: none;
            }

            .page-header h1 {
                font-size: 36px;
            }

            .cart-table {
                font-size: 14px;
            }

            .cart-item-info {
                gap: 10px;
            }

            .cart-item-image {
                width: 60px;
                height: 60px;
            }

            .quantity-input {
                width: 40px;
                height: 25px;
                font-size: 12px;
            }

            .quantity-btn {
                width: 25px;
                height: 25px;
                font-size: 14px;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 40px 20px;
            }

            .cart-card {
                padding: 30px 20px;
            }

            .cart-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
                text-align: center;
            }

            .nav-icons a span {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .page-header {
                padding: 50px 20px;
            }

            .page-header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="navbar">
        <div class="nav-wrapper">
            <div class="logo-text">Techify</div>
            <nav>
                <ul class="nav-menu">
                    <li><a href="index/index.php">HOME</a></li>
                    <li><a href="products.php" class="active">SHOP</a></li>
                    <li><a href="index/index.php#hotdiscounts">HOT DISCOUNTS</a></li>
                    <li><a href="index/index.php#features">FEATURES</a></li>
                    <li><a href="index/index.php#contact">CONTACT US</a></li>
                </ul>
            </nav>
            <div class="nav-icons">
                <a href="profile/profile1.php"><span>Profile</span></a>
                <button class="icon-btn">🔍</button>
                <button class="icon-btn cart-btn">
                    🛒
                    <span class="cart-badge">3</span>
                </button>
            </div>
        </div>
    </header>
    <!-- Hero Section -->
    <div class="page-header">
        <h1>Shopping Cart</h1>
        <p>Review and manage your items</p>
    </div>
    <!-- Main Content -->
    <div class="container">
        <a href="products.php" class="back-button">
            ← Continue Shopping
        </a>
        <div class="cart-card">
            <?php if (empty($cart_items)): ?>
                <!-- Empty Cart Message -->
                <div class="empty-cart">
                    <i>🛒</i>
                    <h3>Your Cart is Empty</h3>
                    <p>Looks like you haven't added anything to your cart yet</p>
                    <a href="products.php" class="btn btn-primary">START SHOPPING</a>
                </div>
            <?php else: ?>
                <!-- Cart Table -->
                <form method="POST" action="update_cart.php">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="cart-item-info">
                                            <img src="images/<?= htmlspecialchars($item['image']) ?>"
                                                alt="<?= htmlspecialchars($item['name']) ?>" class="cart-item-image">
                                            <div class="cart-item-details">
                                                <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="cart-item-price">£<?= number_format($item['price'], 2) ?></td>
                                    <td>
                                        <div class="cart-quantity">
                                            <button type="button" class="quantity-btn minus"
                                                onclick="updateQuantity(<?= $item['id'] ?>, -1)">-</button>
                                            <input type="number" name="quantities[<?= $item['id'] ?>]"
                                                value="<?= $item['quantity'] ?>" min="1" class="quantity-input">
                                            <button type="button" class="quantity-btn plus"
                                                onclick="updateQuantity(<?= $item['id'] ?>, 1)">+</button>
                                        </div>
                                    </td>
                                    <td class="cart-subtotal">£<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                    <td>
                                        <button type="button" class="cart-remove"
                                            onclick="removeItem(<?= $item['id'] ?>)">🗑️</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="cart-actions">
                        <button type="submit" class="btn btn-secondary">Update Cart</button>
                        <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                    </div>
                </form>
                <!-- Cart Summary -->
                <div class="cart-summary">
                    <h3>Cart Summary</h3>
                    <div class="summary-row">
                        <div class="summary-label">Subtotal</div>
                        <div class="summary-value">£<?= number_format($cart_total, 2) ?></div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label">Shipping</div>
                        <div class="summary-value">£0.00</div>
                    </div>
                    <div class="summary-row cart-total">
                        <div class="summary-label">Total</div>
                        <div class="summary-value">£<?= number_format($cart_total, 2) ?></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Techify</h3>
                <p>Your trusted electronics store for laptops, headphones and the latest technology</p>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>📍 Amman, Jordan</p>
                <p>📞 +962 7 0000 0000</p>
                <p>✉️ support@techify.com</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="index.php">Home</a>
                <a href="products.php">Shop</a>
                <a href="discounts.php">Hot Discount</a>
                <a href="features.php">Features</a>
                <a href="contact.php">Contact</a>
            </div>
        </div>
    </footer>
    <script>
        // Function to update quantity via AJAX (optional, for better UX)
        function updateQuantity(cartId, change) {
            const input = document.querySelector(`input[name='quantities[${cartId}]']`);
            let newQuantity = parseInt(input.value) + change;
            if (newQuantity < 1) newQuantity = 1;
            input.value = newQuantity;
            // Optional: Submit form automatically or via AJAX
            // document.querySelector('form').submit();
        }

        // Function to remove item via AJAX (optional, for better UX)
        function removeItem(cartId) {
            if (confirm('Are you sure you want to remove this item?')) {
                // Create a hidden form and submit to remove_item.php
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'remove_item.php';
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'cart_id';
                input.value = cartId;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>