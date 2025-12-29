<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = 'checkout.php';
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];


require_once __DIR__ . '/includes/db.connection.php';
$db = Database::getInstance();
$conn = $db->getConnection();


$stmt = $conn->prepare("
    SELECT c.id AS cart_id, c.product_id, c.quantity, 
           p.name, p.price, p.image,
           (c.quantity * p.price) AS subtotal
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cartItems = $result->fetch_all(MYSQLI_ASSOC);


if (empty($cartItems)) {
    header("Location: cart.php");
    exit;
}


$totalPrice = 0;
$totalQuantity = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['subtotal'];
    $totalQuantity += $item['quantity'];
}
$tax = $totalPrice * 0.15; 
$grandTotal = $totalPrice + $tax;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout - Techify</title>
    <link rel="stylesheet" href="products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ================= CHECKOUT SPECIFIC STYLES ================= */
        .checkout-section {
            padding: 60px 0 80px;
            background: var(--bg-light);
        }

        .checkout-wrapper {
            display: grid;
            grid-template-columns: 1fr 450px;
            gap: 30px;
            margin-top: 30px;
        }

        /* ================= CHECKOUT FORM ================= */
        .checkout-form-wrapper {
            background: var(--white);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            border: 2px solid rgba(254,164,11,0.1);
        }

        .checkout-form-wrapper h2 {
            font-size: 28px;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 800;
            position: relative;
            padding-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .checkout-form-wrapper h2 i {
            color: var(--accent-color);
        }

        .checkout-form-wrapper h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, var(--accent-color), var(--gold-color));
            border-radius: 2px;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .form-section h3 {
            font-size: 18px;
            color: var(--primary-color);
            margin-bottom: 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-section h3 i {
            color: var(--accent-color);
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group label span {
            color: #e74c3c;
            margin-left: 3px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #eee;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: var(--white);
            color: var(--primary-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(254,164,11,0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Card Input Special Styling */
        .card-input {
            position: relative;
        }

        .card-input i {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 20px;
        }

        .card-input input {
            padding-right: 50px;
        }

        /* Security Notice */
        .security-notice {
            background: linear-gradient(135deg, rgba(254,164,11,0.1), rgba(132,100,28,0.1));
            border-left: 4px solid var(--accent-color);
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .security-notice i {
            font-size: 24px;
            color: var(--accent-color);
        }

        .security-notice p {
            font-size: 13px;
            color: var(--primary-color);
            margin: 0;
            line-height: 1.6;
        }

        .security-notice strong {
            color: var(--accent-color);
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 18px 40px;
            font-size: 16px;
            margin-top: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        /* ================= ORDER SUMMARY ================= */
        .order-summary {
            background: var(--white);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            border: 2px solid rgba(254,164,11,0.1);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .order-summary h3 {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 25px;
            font-weight: 800;
            position: relative;
            padding-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .order-summary h3 i {
            color: var(--accent-color);
        }

        .order-summary h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, var(--accent-color), var(--gold-color));
            border-radius: 2px;
        }

        /* Order Items */
        .order-items {
            margin-bottom: 25px;
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .order-items::-webkit-scrollbar {
            width: 6px;
        }

        .order-items::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 10px;
        }

        .order-items::-webkit-scrollbar-thumb {
            background: var(--accent-color);
            border-radius: 10px;
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

        /* Summary Calculations */
        .summary-calculations {
            padding-top: 20px;
            border-top: 2px solid rgba(254,164,11,0.1);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            font-size: 15px;
        }

        .summary-row span:first-child {
            color: var(--text-light);
            font-weight: 600;
        }

        .summary-row span:last-child {
            font-weight: 800;
            color: var(--primary-color);
        }

        .summary-row.total {
            margin-top: 15px;
            padding-top: 20px;
            border-top: 2px solid rgba(254,164,11,0.2);
            font-size: 20px;
        }

        .summary-row.total span:last-child {
            color: var(--accent-color);
            font-size: 24px;
        }

        /* Trust Badges */
        .trust-badges {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #f0f0f0;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .trust-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-light);
            padding: 8px 12px;
            background: #f9f9f9;
            border-radius: 20px;
        }

        .trust-badge i {
            color: #4caf50;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 1200px) {
            .checkout-wrapper {
                grid-template-columns: 1fr 400px;
            }
        }

        @media (max-width: 992px) {
            .checkout-wrapper {
                grid-template-columns: 1fr;
            }

            .order-summary {
                position: static;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .checkout-form-wrapper,
            .order-summary {
                padding: 25px 20px;
            }

            .checkout-form-wrapper h2,
            .order-summary h3 {
                font-size: 22px;
            }

            .order-item {
                flex-direction: column;
                text-align: center;
            }

            .order-item-image {
                margin: 0 auto;
            }

            .order-item-price {
                text-align: center;
            }
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
                    <span class="cart-badge"><?= htmlspecialchars($totalQuantity) ?></span>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- ================= PAGE HEADER ================= -->
<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-lock"></i> Secure Checkout</h1>
        <p>Complete your purchase safely and securely</p>
    </div>
</section>

<!-- ================= CHECKOUT SECTION ================= -->
<section class="checkout-section">
    <div class="container">
        
        <div class="checkout-wrapper">
            
            <!-- Checkout Form -->
            <div class="checkout-form-wrapper">
                <h2><i class="fas fa-credit-card"></i> Payment Information</h2>

                <div class="security-notice">
                    <i class="fas fa-shield-alt"></i>
                    <p>
                        <strong>Demo Payment Mode:</strong> This is a simulated checkout for testing purposes. 
                        No real payment will be processed. Use any test card details.
                    </p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div style="background: #ffebee; color: #c62828; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
                        Error: 
                        <?php 
                        switch($_GET['error']) {
                            case 'missing_fields': echo "Please fill all required fields."; break;
                            case 'cart_empty': echo "Your cart is empty."; break;
                            case 'order_failed': echo "Order creation failed. Please try again."; break;
                            default: echo "An error occurred. Please try again.";
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <form id="checkoutForm" method="POST" action="process_checkout.php">
                    
                    <!-- Card Details Section -->
                    <div class="form-section">
                        <h3><i class="fas fa-credit-card"></i> Card Details</h3>
                        
                        <div class="form-group">
                            <label>Cardholder Name <span>*</span></label>
                            <input type="text" name="cardholder_name" placeholder="John Doe" required>
                        </div>

                        <div class="form-group card-input">
                            <label>Card Number <span>*</span></label>
                            <input type="text" name="card_number" placeholder="1234 5678 9012 3456" 
                                   maxlength="19" required pattern="[0-9\s]{13,19}">
                            <i class="fas fa-credit-card"></i>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Expiry Date <span>*</span></label>
                                <input type="text" name="expiry_date" placeholder="MM/YYYY" 
                                       maxlength="7" required pattern="[0-9]{2}/[0-9]{4}">
                            </div>
                            <div class="form-group card-input">
                                <label>CVV <span>*</span></label>
                                <input type="text" name="cvv" placeholder="123" 
                                       maxlength="4" required pattern="[0-9]{3,4}">
                                <i class="fas fa-lock"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Address Section -->
                    <div class="form-section">
                        <h3><i class="fas fa-map-marker-alt"></i> Billing Address</h3>
                        
                        <div class="form-group">
                            <label>Full Name <span>*</span></label>
                            <input type="text" name="billing_name" placeholder="Enter your full name" required>
                        </div>

                        <div class="form-group">
                            <label>Email Address <span>*</span></label>
                            <input type="email" name="billing_email" placeholder="your.email@example.com" required>
                        </div>

                        <div class="form-group">
                            <label>Phone Number <span>*</span></label>
                            <input type="tel" name="billing_phone" placeholder="+962 7X XXX XXXX" required>
                        </div>

                        <div class="form-group">
                            <label>Complete Address <span>*</span></label>
                            <textarea name="billing_address" placeholder="Street address, City, Postal Code, Country" 
                                      required></textarea>
                        </div>
                    </div>

                    <!-- Shipping Address Section -->
                    <div class="form-section">
                        <h3><i class="fas fa-shipping-fast"></i> Shipping Address</h3>
                        
                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: 10px; text-transform: none;">
                                <!-- ✅ تم إضافة name و value هنا -->
                                <input type="checkbox" id="sameAsBilling" name="sameAsBilling" value="on" style="width: auto;" checked>
                                Same as billing address
                            </label>
                        </div>

                        <div id="shippingFields" style="display: none;">
                            <div class="form-group">
                                <label>Full Name <span>*</span></label>
                                <input type="text" name="shipping_name" placeholder="Recipient name">
                            </div>

                            <div class="form-group">
                                <label>Phone Number <span>*</span></label>
                                <input type="tel" name="shipping_phone" placeholder="+962 7X XXX XXXX">
                            </div>

                            <div class="form-group">
                                <label>Complete Address <span>*</span></label>
                                <textarea name="shipping_address" placeholder="Street address, City, Postal Code, Country"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields for order details -->
                    <input type="hidden" name="total_amount" value="<?= number_format($grandTotal, 2, '.', '') ?>">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">

                    <button type="submit" class="btn btn-primary submit-btn">
                        <i class="fas fa-lock"></i>
                        Complete Fake Payment ($<?= number_format($grandTotal, 2) ?>)
                    </button>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h3><i class="fas fa-shopping-bag"></i> Order Summary</h3>

                <div class="order-items">
                    <?php foreach($cartItems as $item): ?>
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
                                <p>Qty: <?= $item['quantity'] ?> × $<?= number_format($item['price'], 2) ?></p>
                            </div>
                            <div class="order-item-price">
                                $<?= number_format($item['subtotal'], 2) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-calculations">
                    <div class="summary-row">
                        <span>Subtotal (<?= $totalQuantity ?> items):</span>
                        <span>$<?= number_format($totalPrice, 2) ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span style="color: #4caf50;">Free</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Tax (VAT 15%):</span>
                        <span>$<?= number_format($tax, 2) ?></span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>$<?= number_format($grandTotal, 2) ?></span>
                    </div>
                </div>

                <div class="trust-badges">
                    <div class="trust-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure Payment</span>
                    </div>
                    <div class="trust-badge">
                        <i class="fas fa-truck"></i>
                        <span>Free Shipping</span>
                    </div>
                    <div class="trust-badge">
                        <i class="fas fa-undo"></i>
                        <span>Easy Returns</span>
                    </div>
                </div>
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

<script>
// Toggle shipping address fields
document.getElementById('sameAsBilling').addEventListener('change', function() {
    const shippingFields = document.getElementById('shippingFields');
    if(this.checked) {
        shippingFields.style.display = 'none';
        // Clear shipping fields
        shippingFields.querySelectorAll('input, textarea').forEach(field => {
            field.removeAttribute('required');
            field.value = '';
        });
    } else {
        shippingFields.style.display = 'block';
        shippingFields.querySelectorAll('input, textarea').forEach(field => {
            field.setAttribute('required', 'required');
        });
    }
});

// Format card number with spaces
document.querySelector('input[name="card_number"]').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Format expiry date
document.querySelector('input[name="expiry_date"]').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if(value.length >= 2) {
        value = value.slice(0, 2) + '/' + value.slice(2, 6);
    }
    e.target.value = value;
});

// Only numbers for CVV
document.querySelector('input[name="cvv"]').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '');
});

// Form validation before submit
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Basic validation
    const cardNumber = document.querySelector('input[name="card_number"]').value.replace(/\s/g, '');
    const cvv = document.querySelector('input[name="cvv"]').value;
    
    if(cardNumber.length < 13 || cardNumber.length > 19) {
        alert('Please enter a valid card number');
        return;
    }
    
    if(cvv.length < 3 || cvv.length > 4) {
        alert('Please enter a valid CVV');
        return;
    }
    
    // If validation passes, submit the form
    this.submit();
});
</script>

</body>
</html>