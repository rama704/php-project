<?php
session_start();
require_once __DIR__ . '/includes/db.connection.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit;
}
$product_id =  $_GET['id'];
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
    $stockText .= " (Low stock)";
}

/* ====== Reviews Data ====== */
// Average Rating
$avgRatingQuery = "SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = ?";
$avgStmt = $conn->prepare($avgRatingQuery);
$avgStmt->bind_param("i", $product_id);
$avgStmt->execute();
$avgResult = $avgStmt->get_result();
$avgRatingData = $avgResult->fetch_assoc();
$averageRating = $avgRatingData['avg_rating'] ? round($avgRatingData['avg_rating'], 2) : 0;

// Reviews Count
$reviewsCountQuery = "SELECT COUNT(*) as count FROM reviews WHERE product_id = ?";
$countStmt = $conn->prepare($reviewsCountQuery);
$countStmt->bind_param("i", $product_id);
$countStmt->execute();
$countResult = $countStmt->get_result();
$reviewsCount = $countResult->fetch_assoc()['count'];

// Rating Distribution
$distributionQuery = "SELECT rating, COUNT(*) as count FROM reviews WHERE product_id = ? GROUP BY rating ORDER BY rating DESC";
$distStmt = $conn->prepare($distributionQuery);
$distStmt->bind_param("i", $product_id);
$distStmt->execute();
$distResult = $distStmt->get_result();
$ratingDistribution = [];
while ($row = $distResult->fetch_assoc()) {
    $ratingDistribution[$row['rating']] = $row['count'];
}
// Ensure all ratings from 5 to 1 exist in the array, even if count is 0
for ($i = 5; $i >= 1; $i--) {
    if (!isset($ratingDistribution[$i])) {
        $ratingDistribution[$i] = 0;
    }
}

// Reviews List
$reviewsListQuery = "SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC";
$listStmt = $conn->prepare($reviewsListQuery);
$listStmt->bind_param("i", $product_id);
$listStmt->execute();
$reviewsListResult = $listStmt->get_result();
$reviewsList = [];
while ($row = $reviewsListResult->fetch_assoc()) {
    $reviewsList[] = $row;
}

// Check if User can Review (Must have purchased the product)
$userCanReview = false;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $checkPurchaseQuery = "SELECT oi.product_id FROM order_items oi
JOIN orders o ON oi.order_id = o.id
WHERE o.user_id = ? AND oi.product_id = ?";
    $checkStmt = $conn->prepare($checkPurchaseQuery);
    $checkStmt->bind_param("ii", $userId, $product_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    if ($checkResult->num_rows > 0) {
        $userCanReview = true;
    }
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

        /* ================= PRODUCT DETAIL CARD ================= */
        .product-detail-card {
            background: var(--white);
            border-radius: 15px;
            padding: 50px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            border: 2px solid rgba(254, 164, 11, 0.1);
            position: relative;
            overflow: hidden;
        }

        .product-detail-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(254, 164, 11, 0.03) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ================= PRODUCT LAYOUT ================= */
        .product-layout {
            display: grid;
            grid-template-columns: 45% 55%;
            gap: 60px;
            margin-bottom: 50px;
            position: relative;
            z-index: 1;
        }

        /* ================= PRODUCT IMAGE SECTION ================= */
        .product-image-section {
            position: relative;
        }

        .discount-badge {
            position: absolute;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, var(--accent-color), var(--gold-color));
            color: var(--white);
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 14px;
            z-index: 2;
            box-shadow: 0 4px 15px rgba(254, 164, 11, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-image-wrapper {
            margin-top: 50px;
            background: linear-gradient(135deg, #f5f5f5, #fafafa);
            border-radius: 15px;
            padding: 50px;
            text-align: center;
            border: 2px solid rgba(254, 164, 11, 0.1);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .product-image-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(254, 164, 11, 0.05), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-image-wrapper:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(254, 164, 11, 0.15);
        }

        .product-image-wrapper:hover::before {
            opacity: 1;
        }

        .product-image {
            width: 100%;
            max-width: 400px;
            height: 400px;
            object-fit: contain;
            transition: transform 0.4s ease;
            position: relative;
            z-index: 1;
        }

        .product-image-wrapper:hover .product-image {
            transform: scale(1.05);
        }

        /* ================= PRODUCT INFO SECTION ================= */
        .product-info-section {
            padding-top: 10px;
        }

        .product-info-section h2 {
            font-size: 36px;
            color: var(--primary-color);
            margin-bottom: 20px;
            font-weight: 800;
            line-height: 1.3;
        }

        .product-description {
            color: var(--text-light);
            line-height: 1.8;
            margin-bottom: 30px;
            font-size: 16px;
        }

        /* ================= PRICE SECTION ================= */
        .price-section {
            display: flex;
            align-items: baseline;
            gap: 15px;
            margin-bottom: 30px;
        }

        .current-price {
            font-size: 48px;
            color: var(--accent-color);
            font-weight: 800;
        }

        .original-price {
            font-size: 24px;
            color: var(--text-light);
            text-decoration: line-through;
        }

        /* ================= STOCK INFO ================= */
        .stock-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 35px;
            font-size: 15px;
            color: var(--primary-color);
            font-weight: 600;
        }

        .stock-badge {
            background: #4caf50;
            color: var(--white);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 3px 10px rgba(76, 175, 80, 0.3);
        }

        .stock-badge.low-stock {
            background: var(--accent-color);
            box-shadow: 0 3px 10px rgba(254, 164, 11, 0.3);
        }

        .stock-badge.out-of-stock {
            background: #e74c3c;
            box-shadow: 0 3px 10px rgba(231, 76, 60, 0.3);
        }

        /* ================= ACTION BUTTONS ================= */
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 40px;
        }

        .btn {
            padding: 16px 40px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
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
            flex: 1;
            box-shadow: 0 4px 20px rgba(254, 164, 11, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(254, 164, 11, 0.5);
        }

        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
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

        /* ================= SUCCESS MESSAGE ================= */
        .success-message {
            display: none;
            background: linear-gradient(135deg, #4caf50, #45a049);
            color: var(--white);
            padding: 16px 25px;
            border-radius: 10px;
            margin-top: 25px;
            text-align: center;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .success-message.show {
            display: block;
            animation: slideDown 0.4s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ================= DETAILS SECTION ================= */
        .details-section {
            margin-top: 60px;
            padding-top: 50px;
            border-top: 2px solid rgba(254, 164, 11, 0.1);
            position: relative;
            z-index: 1;
        }

        .details-section h3 {
            font-size: 28px;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 800;
            position: relative;
            padding-bottom: 15px;
        }

        .details-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, var(--accent-color), var(--gold-color));
            border-radius: 2px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table tr {
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.3s ease;
        }

        .details-table tr:hover {
            background: rgba(254, 164, 11, 0.03);
        }

        .details-table tr:last-child {
            border-bottom: none;
        }

        .details-table td {
            padding: 18px 0;
            font-size: 15px;
        }

        .details-table td:first-child {
            font-weight: 700;
            color: var(--primary-color);
            width: 200px;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        .details-table td:last-child {
            color: var(--text-light);
        }

        /* ================= REVIEWS SECTION ================= */
        .reviews-section {
            margin-top: 60px;
            padding-top: 50px;
            border-top: 2px solid rgba(254, 164, 11, 0.1);
            position: relative;
            z-index: 1;
        }

        .reviews-section h3 {
            font-size: 28px;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 800;
            position: relative;
            padding-bottom: 15px;
        }

        .reviews-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, var(--accent-color), var(--gold-color));
            border-radius: 2px;
        }

        .review-stats {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 25px;
            margin-bottom: 40px;
            align-items: center;
        }

        .review-summary {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .average-rating {
            font-size: 48px;
            font-weight: 800;
            color: var(--accent-color);
        }

        .total-reviews {
            font-size: 16px;
            color: var(--text-light);
        }

        .rating-stars {
            display: flex;
            gap: 5px;
            margin-top: 5px;
        }

        .rating-stars i {
            color: #ddd;
            font-size: 18px;
        }

        .rating-stars i.filled {
            color: var(--accent-color);
        }

        .rating-distribution {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 100%;
        }

        .distribution-bar {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .distribution-label {
            width: 30px;
            font-size: 14px;
            color: var(--primary-color);
            font-weight: 600;
        }

        .distribution-progress {
            flex: 1;
            background: #eee;
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }

        .distribution-count {
            width: 40px;
            font-size: 14px;
            color: var(--text-light);
            text-align: right;
        }

        .distribution-progress-bar {
            height: 100%;
            background: var(--accent-color);
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 8px;
            color: var(--white);
            font-size: 10px;
            font-weight: bold;
            min-width: 0;
            /* Allow it to shrink below its content */
            transition: width 0.4s ease;
        }

        .review-list {
            margin-top: 50px;
        }

        .review-list h4 {
            font-size: 20px;
            color: var(--primary-color);
            margin-bottom: 25px;
            font-weight: 700;
        }

        .review-item {
            background: var(--white);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(254, 164, 11, 0.05);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .review-author {
            font-weight: 700;
            color: var(--primary-color);
        }

        .review-date {
            font-size: 14px;
            color: var(--text-light);
        }

        .review-rating {
            display: flex;
            gap: 3px;
            margin-bottom: 10px;
        }

        .review-rating i {
            color: #ddd;
            font-size: 16px;
        }

        .review-rating i.filled {
            color: var(--accent-color);
        }

        .review-comment {
            color: var(--primary-color);
            line-height: 1.7;
        }

        /* ================= REVIEW FORM ================= */
        .review-form {
            margin-top: 50px;
            background: var(--white);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(254, 164, 11, 0.05);
        }

        .review-form h4 {
            font-size: 20px;
            color: var(--primary-color);
            margin-bottom: 25px;
            font-weight: 700;
        }

        .review-form-group {
            margin-bottom: 20px;
        }

        .review-form-group label {
            display: block;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .review-form-group input,
        .review-form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: var(--bg-light);
            color: var(--primary-color);
        }

        .review-form-group input:focus,
        .review-form-group textarea:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(254, 164, 11, 0.1);
        }

        .review-form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .review-form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .review-message {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .review-message.success {
            background: rgba(76, 175, 80, 0.1);
            color: #4caf50;
            border: 1px solid rgba(76, 175, 80, 0.2);
        }

        .review-message.error {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.2);
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

            .product-layout {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .page-header h1 {
                font-size: 36px;
            }

            .review-stats {
                grid-template-columns: 1fr;
                /* Stack summary and distribution on smaller screens */
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 40px 20px;
            }

            .product-detail-card {
                padding: 30px 20px;
            }

            .product-info-section h2 {
                font-size: 28px;
            }

            .current-price {
                font-size: 36px;
            }

            .action-buttons {
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

            .details-table td:first-child {
                width: 140px;
            }
        }

        @media (max-width: 576px) {
            .page-header {
                padding: 50px 20px;
            }

            .page-header h1 {
                font-size: 28px;
            }

            .product-image-wrapper {
                padding: 30px 20px;
            }

            .product-image {
                height: 280px;
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
                <a href="cart.php" class="icon-btn cart-btn">
                               🛒
                          <span class="cart-badge">3</span>
                        </a>
            </div>
        </div>
    </header>
    <!-- Hero Section -->
    <div class="page-header">
        <h1>Product Details</h1>
        <p>Discover the latest electronics and technology</p>
    </div>
    <!-- Main Content -->
    <div class="container">
        <a href="products.php" class="back-button">
            ← Back to Shop
        </a>
        <div class="product-detail-card">
            <form id="productForm" method="POST" action="checkout.php">
                <div class="product-layout">
                    <!-- Product Image Section -->
                    <div class="product-image-section">
                        <?php if ($discountPercent > 0): ?>
                            <div class="discount-badge">-<?= $discountPercent ?>%</div>
                        <?php endif; ?>
                        <div class="product-image-wrapper">
                            <img src="images/<?= htmlspecialchars($product['image']) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                        </div>
                    </div>
                    <!-- Product Info Section -->
                    <div class="product-info-section">
                        <h2><?= htmlspecialchars($product['name']) ?></h2>
                        <!-- Product Rating under Name -->
                        <div class="product-rating">
                            <div class="average-rating"><?= number_format($averageRating, 1) ?></div>
                            <div class="total-reviews"><?= $reviewsCount ?> reviews</div>
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?= ($i <= round($averageRating)) ? 'filled' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="product-description">
                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                        </p>
                        <div class="price-section">
                            <span class="current-price">£<?= number_format($finalPrice, 2) ?></span>
                            <?php if ($discountPercent > 0): ?>
                                <span class="original-price">£<?= number_format($product['price'], 2) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="stock-info">
                            <span>Stock Available:</span>
                            <span class="<?= $stockClass ?>">
                                <?= $stockText ?> </span>
                        </div>
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                        <input type="hidden" name="product_price" value="<?= $finalPrice ?>">
                        <div class="action-buttons">
                            <button type="submit" class="btn btn-primary" <?= $product['stock'] == 0 ? 'disabled' : '' ?>>
                                🛒 Add to Cart
                            </button>
                            <button type="button" class="btn btn-secondary"
                                onclick="window.location.href='products.php'">
                                Continue Shopping
                            </button>
                            <!-- Buy Now Button -->
                            <button type="button" class="btn btn-primary" onclick="buyNow()">
                                🛒 Buy Now
                                 
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
                        <?php if ($discountPercent > 0): ?>
                            <tr>
                                <td>Discount Price</td>
                                <td>£<?= number_format($finalPrice, 2) ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td>Stock Quantity</td>
                            <td><?= $product['stock'] ?></td>
                        </tr>
                        <tr>
                            <td>Date Added</td>
                            <td><?= date("F d, Y", strtotime($product['created_at'])) ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Reviews Section -->
                <div class="reviews-section">
                    <h3>Customer Reviews</h3>
                    <div class="review-stats">
                        <div class="review-summary">
                            <div class="average-rating"><?= number_format($averageRating, 1) ?></div>
                            <div class="total-reviews"><?= $reviewsCount ?> reviews</div>
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?= ($i <= round($averageRating)) ? 'filled' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="rating-distribution">
                            <?php foreach ($ratingDistribution as $star => $count): ?>
                                <?php $percentage = $reviewsCount > 0 ? ($count / $reviewsCount) * 100 : 0; ?>
                                <div class="distribution-bar">
                                    <div class="distribution-label"><?= $star ?>★</div>
                                    <div class="distribution-progress">
                                        <div class="distribution-progress-bar" style="width: <?= $percentage ?>%">
                                            <?= $count ?></div>
                                    </div>
                                    <div class="distribution-count"><?= $count ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="review-list">
                        <h4>Latest Reviews</h4>
                        <?php if (!empty($reviewsList)): ?>
                            <?php foreach ($reviewsList as $review): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="review-author"><?= htmlspecialchars($review['name']) ?></div>
                                        <div class="review-date"><?= date("F d, Y", strtotime($review['created_at'])) ?></div>
                                    </div>
                                    <div class="review-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?= ($i <= $review['rating']) ? 'filled' : '' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="review-comment">
                                        <?= htmlspecialchars($review['comment']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No reviews yet. Be the first to review this product!</p>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($userCanReview): ?>
                            <div class="review-form">
                                <h4>Write a Review</h4>
                                <form id="submitReviewForm" method="POST" action="submit_review.php">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <div class="review-form-group">
                                        <label for="review_rating">Your Rating</label>
                                        <div class="rating-stars" id="ratingInput">
                                            <i class="fas fa-star" data-value="1"></i>
                                            <i class="fas fa-star" data-value="2"></i>
                                            <i class="fas fa-star" data-value="3"></i>
                                            <i class="fas fa-star" data-value="4"></i>
                                            <i class="fas fa-star" data-value="5"></i>
                                        </div>
                                        <input type="hidden" name="rating" id="ratingValue" value="0" required>
                                    </div>
                                    <div class="review-form-group">
                                        <label for="review_comment">Your Review</label>
                                        <textarea name="comment" id="review_comment"
                                            placeholder="Share your experience with this product..." required></textarea>
                                    </div>
                                    <div class="review-form-actions">
                                        <button type="submit" class="btn btn-primary">Submit Review</button>
                                    </div>
                                    <div id="reviewMessage"></div>
                                </form>
                            </div>
                            <script>
                                // Simple star rating selection
                                document.querySelectorAll('#ratingInput i').forEach(star => {
                                    star.addEventListener('click', function () {
                                        const value = parseInt(this.getAttribute('data-value'));
                                        document.querySelectorAll('#ratingInput i').forEach(s => s.classList.remove('filled'));
                                        for (let i = 1; i <= value; i++) {
                                            document.querySelector(`#ratingInput i[data-value="${i}"]`).classList.add('filled');
                                        }
                                        document.getElementById('ratingValue').value = value;
                                    });
                                });

                                // Optional: Handle form submission via JS if needed
                                document.getElementById('submitReviewForm').addEventListener('submit', function (e) {
                                    e.preventDefault(); // Prevent default form submission for now
                                    const formData = new FormData(this);
                                    fetch('submit_review.php', {
                                        method: 'POST',
                                        body: formData
                                    })
                                        .then(response => response.json())
                                        .then(data => {
                                            const messageDiv = document.getElementById('reviewMessage');
                                            if (data.success) {
                                                messageDiv.className = 'review-message success';
                                                messageDiv.textContent = data.message;
                                                this.reset(); // Reset form
                                                // Optional: Reload reviews section or add the new review dynamically
                                            } else {
                                                messageDiv.className = 'review-message error';
                                                messageDiv.textContent = data.message;
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            document.getElementById('reviewMessage').className = 'review-message error';
                                            document.getElementById('reviewMessage').textContent = 'An error occurred. Please try again.';
                                        });
                                });
                            </script>
                        <?php else: ?>
                            <div class="review-message error">
                                You must purchase this product before you can leave a review.
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="review-message error">
                            Please <a href="login.php">log in</a> to write a review.
                        </div>
                    <?php endif; ?>
                </div> <!-- End Reviews Section -->

            </form>
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
        function buyNow() {
            // This function will add the product to the cart and redirect to checkout
            document.getElementById('productForm').submit();
            form.action='checkout.php';
            form.submit(); // Submit the form to add to cart
            // You might want to redirect to checkout page after adding to cart
            // window.location.href = 'checkout.php';
        }
    </script>
</body>

</html>