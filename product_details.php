<?php
session_start();
require_once __DIR__ . '/includes/db.connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = (int) $_GET['id'];

$db = Database::getInstance();
$conn = $db->getConnection();

/* ====== Get Product ====== */
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("Product not found");
}

/* ====== Discount ====== */
$discountPercent = 0;
$finalPrice = $product['price'];
if (!empty($product['discount_price']) && $product['discount_price'] < $product['price']) {
    $discountPercent = round((($product['price'] - $product['discount_price']) / $product['price']) * 100);
    $finalPrice = $product['discount_price'];
}

/* ====== Stock Status ====== */
$stockClass = "stock-badge";
$stockText = $product['stock'] . " units";
if ($product['stock'] == 0) {
    $stockClass .= " out-of-stock";
    $stockText = "Out of stock";
} elseif ($product['stock'] <= 20) {
    $stockClass .= " low-stock";
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - Techify</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }

        /* Header */
        header {
            background-color: #fff;
            padding: 15px 50px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .logo span:first-child {
            color: #2d2d2d;
        }

        .logo span:last-child {
            color: #f39c12;
        }

        nav {
            display: flex;
            gap: 30px;
        }

        nav a {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            transition: color 0.3s;
            text-transform: uppercase;
            font-size: 14px;
        }

        nav a:hover, nav a.active {
            color: #f39c12;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .header-actions button {
            padding: 8px 20px;
            border: 1px solid #e0e0e0;
            background: #fff;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }

        .header-actions button:hover {
            background: #f9f9f9;
            border-color: #f39c12;
        }

        .cart-btn {
            position: relative;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #f39c12;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
            color: white;
            padding: 50px;
            text-align: center;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .hero p {
            color: #f39c12;
            font-size: 18px;
            font-weight: 400;
        }

        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #666;
            text-decoration: none;
            margin-bottom: 25px;
            font-size: 14px;
            transition: color 0.3s;
        }

        .back-button:hover {
            color: #f39c12;
        }

        .product-detail-card {
            background: white;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .product-layout {
            display: grid;
            grid-template-columns: 45% 55%;
            gap: 60px;
            margin-bottom: 50px;
        }

        .product-image-section {
            position: relative;
        }

        .discount-badge {
            position: absolute;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, #d68910 0%, #b87503 100%);
            color: white;
            padding: 6px 14px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 13px;
            z-index: 1;
        }

        .product-image-wrapper {
            margin-top: 40px;
            background: #fafafa;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            border: 1px solid #eee;
        }

        .product-image {
            width: 100%;
            max-width: 380px;
            height: 380px;
            object-fit: contain;
        }

        .product-info-section {
            padding-top: 10px;
        }

        .product-info-section h2 {
            font-size: 30px;
            color: #2d2d2d;
            margin-bottom: 15px;
            font-weight: 600;
            line-height: 1.3;
        }

        .product-description {
            color: #666;
            line-height: 1.7;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .price-section {
            display: flex;
            align-items: baseline;
            gap: 12px;
            margin-bottom: 25px;
        }

        .current-price {
            font-size: 40px;
            color: #f39c12;
            font-weight: 700;
        }

        .original-price {
            font-size: 22px;
            color: #b0b0b0;
            text-decoration: line-through;
        }

        .stock-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            font-size: 15px;
            color: #555;
        }

        .stock-badge {
            background: #4caf50;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
        }

        .stock-badge.low-stock {
            background: #f39c12;
        }

        .stock-badge.out-of-stock {
            background: #e74c3c;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 35px;
        }

        .btn {
            padding: 14px 35px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
            flex: 1;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
        }

        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-secondary {
            background: white;
            color: #555;
            border: 2px solid #e0e0e0;
        }

        .btn-secondary:hover {
            border-color: #f39c12;
            color: #f39c12;
        }

        /* Product Details Table */
        .details-section {
            margin-top: 50px;
            padding-top: 40px;
            border-top: 1px solid #eee;
        }

        .details-section h3 {
            font-size: 22px;
            color: #2d2d2d;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table tr {
            border-bottom: 1px solid #f0f0f0;
        }

        .details-table tr:last-child {
            border-bottom: none;
        }

        .details-table td {
            padding: 16px 0;
            font-size: 15px;
        }

        .details-table td:first-child {
            font-weight: 600;
            color: #555;
            width: 180px;
        }

        .details-table td:last-child {
            color: #777;
        }

        /* Success Message */
        .success-message {
            display: none;
            background: #4caf50;
            color: white;
            padding: 14px 20px;
            border-radius: 4px;
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
        }

        .success-message.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Footer */
        footer {
            background: #1a1a1a;
            color: white;
            padding: 50px;
            margin-top: 80px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 50px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-section h3 {
            color: #f39c12;
            margin-bottom: 18px;
            font-size: 18px;
            font-weight: 600;
        }

        .footer-section p {
            color: #b0b0b0;
            line-height: 1.8;
            font-size: 14px;
        }

        .footer-section a {
            color: #b0b0b0;
            text-decoration: none;
            display: block;
            line-height: 2.2;
            font-size: 14px;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: #f39c12;
        }

        @media (max-width: 968px) {
            .product-layout {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            header {
                padding: 15px 20px;
            }

            nav {
                gap: 15px;
            }

            nav a {
                font-size: 12px;
            }

            .hero h1 {
                font-size: 36px;
            }
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <span>Tech</span><span>ify</span>
        </div>
        <nav>
            <a href="index/index.php">HOME</a>
            <a href="products.php" class="active">SHOP</a>
            <a href="index/index.php#hotdiscounts">HOT DISCOUNTS</a>
            <a href="index/index.php#features">FEATURES</a>
            <a href="index/index.php#contact">CONTACT US</a>
        </nav>
       <div class="header-actions">
    <!-- Profile Button -->
    <button onclick="window.location.href='profile/profile1.php'">Profile</button>

    <!-- Search Button -->
    <button>🔍</button>

    <!-- Cart Button -->
    <button class="cart-btn" onclick="window.location.href='cart.php'">
        🛒
        <span class="cart-badge">3</span>
    </button>
</div>

    </header>

    <!-- Hero Section -->
    <div class="hero">
        <h1>Our Products</h1>
        <p>Discover the latest electronics and technology</p>
    </div>

    <!-- Main Content -->
    <div class="container">
        <a href="products.php" class="back-button">
            ← Back to Shop
        </a>

        <div class="product-detail-card">
            <form id="productForm" method="POST" action="add_to_cart.php">
                <div class="product-layout">
                    <!-- Product Image Section -->
                    <div class="product-image-section">
                        <div class="discount-badge" id="discountBadge">-10%</div>
                        <div class="product-image-wrapper">
                           <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">

                        </div>
                    </div>

                    <!-- Product Info Section -->
                    <div class="product-info-section">
                        <h2><?= htmlspecialchars($product['name']) ?></h2>

                        
                        <p class="product-description" id="productDescription">
                            A high-end smartphone with excellent features
                        </p>

                        <div class="price-section">
                            <span class="current-price">£<?= number_format($finalPrice, 2) ?></span>

                            <span class="original-price" id="originalPrice">£<?= number_format($product['price'], 2) ?></span>

                        </div>

                        <div class="stock-info">
                            <span>Stock Available:</span>
                            <span class="<?= $stockClass ?>"><?= $stockText ?></span>

                        </div>

                        <input type="hidden" name="product_id" id="productId" value="">
                        <input type="hidden" name="product_name" id="hiddenProductName" value="">
                        <input type="hidden" name="product_price" id="hiddenProductPrice" value="">

                        <div class="action-buttons">
                            <button type="submit" class="btn btn-primary" id="addToCartBtn">
                                🛒 Add to Cart
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='shop.php'">
                                Continue Shopping
                            </button>
                        </div>

                        <div class="success-message" id="successMessage">
                            ✓ Product added to cart successfully!
                        </div>
                    </div>
                </div>

                <!-- Product Details Table -->
                <div class="details-section">
                    <h3>Product Specifications</h3>
                    <table class="details-table">
                       <tr>
    <td>Product ID</td>
    <td><?= $product['id'] ?></td>
</tr>
<tr>
    <td>Category</td>
    <td><?= htmlspecialchars($product['category_name']) ?></td>
</tr>
<tr>
    <td>Description</td>
    <td><?= htmlspecialchars($product['description']) ?></td>
</tr>
<tr>
    <td>Original Price</td>
    <td>£<?= number_format($product['price'], 2) ?></td>
</tr>
<tr>
    <td>Discount Price</td>
    <td>£<?= number_format($product['discount_price'], 2) ?></td>
</tr>
<tr>
    <td>Stock Quantity</td>
    <td><?= $product['stock'] ?></td>
</tr>
<tr>
    <td>Image</td>
    <td><?= htmlspecialchars($product['image']) ?></td>
</tr>
<tr>
    <td>Date Added</td>
    <td><?= $product['created_at'] ?></td>
</tr>

                    </table>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
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
                <a href="shop.php">Shop</a>
                <a href="discounts.php">Hot Discount</a>
                <a href="features.php">Features</a>
                <a href="contact.php">Contact</a>
            </div>
        </div>
    </footer>

   
</body>
</html>