<?php
session_start();
require_once __DIR__ . '/includes/db.connection.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to submit a review.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// استلام البيانات
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

// التحقق من صحة البيانات
if ($product_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data. Please fill all fields correctly.']);
    exit;
}

// التحقق مرة أخرى مما إذا كان المستخدم قد اشترى المنتج (لأنه يمكن تجاوز التحقق في الجافاسكريبت)
$db = Database::getInstance();
$conn = $db->getConnection();

$checkPurchaseQuery = "SELECT oi.product_id FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.user_id = ? AND oi.product_id = ?";
$checkStmt = $conn->prepare($checkPurchaseQuery);
$checkStmt->bind_param("ii", $user_id, $product_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'You must purchase this product before you can leave a review.']);
    exit;
}

// إدخال التقييم في قاعدة البيانات
$insertQuery = "INSERT INTO reviews (user_id, product_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())";
$insertStmt = $conn->prepare($insertQuery);
$insertStmt->bind_param("iiis", $user_id, $product_id, $rating, $comment);

if ($insertStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Your review has been submitted successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'An error occurred while submitting your review. Please try again.']);
}

$conn->close();
?>