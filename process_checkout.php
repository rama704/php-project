<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// تضمين اتصال قاعدة البيانات
require_once __DIR__ . '/includes/db.connection.php';
$db = Database::getInstance();
$conn = $db->getConnection();

// التحقق من إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: checkout.php");
    exit;
}

// استلام البيانات
$cardholder_name = trim($_POST['cardholder_name'] ?? '');
$card_number = trim($_POST['card_number'] ?? '');
$expiry_date = trim($_POST['expiry_date'] ?? '');
$cvv = trim($_POST['cvv'] ?? '');
$billing_name = trim($_POST['billing_name'] ?? '');
$billing_email = trim($_POST['billing_email'] ?? '');
$billing_phone = trim($_POST['billing_phone'] ?? '');
$billing_address = trim($_POST['billing_address'] ?? '');
$total_amount = floatval($_POST['total_amount'] ?? 0);

// التحقق من وجود البيانات الأساسية
if (empty($cardholder_name) || empty($card_number) || empty($cvv) || empty($billing_address) || $total_amount <= 0) {
    header("Location: checkout.php?error=missing_fields");
    exit;
}

// 1. إنشاء طلب جديد في جدول orders
// 1. إنشاء طلب جديد في جدول orders
$stmt = $conn->prepare("
    INSERT INTO orders (user_id, total_price, status, created_at)
    VALUES (?, ?, 'completed', NOW())
");
$stmt->execute([$user_id, $total_amount]);
$order_id = $conn->insert_id;

if (!$order_id) {
    header("Location: checkout.php?error=order_failed");
    exit;
}

// 2. جلب عناصر السلة
$stmt = $conn->prepare("
    SELECT c.product_id, c.quantity, p.price
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$result = $stmt->get_result();
$cartItems = $result->fetch_all(MYSQLI_ASSOC);

if (empty($cartItems)) {
    header("Location: checkout.php?error=cart_empty");
    exit;
}

// 3. إدخال عناصر الطلب في order_items
$stmt = $conn->prepare("
    INSERT INTO order_items (order_id, product_id, quantity, price)
    VALUES (?, ?, ?, ?)
");

foreach ($cartItems as $item) {
    $stmt->execute([
        $order_id,
        $item['product_id'],
        $item['quantity'],
        $item['price']
    ]);
}

// 4. تخزين بيانات الدفع في fake_payment_details
$stmt = $conn->prepare("
    INSERT INTO fake_payment_details (order_id, cardholder_name, card_number, expiry_date, cvv, address)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->execute([
    $order_id,
    $cardholder_name,
    $card_number,
    $expiry_date,
    $cvv,
    $billing_address
]);

// 5. مسح السلة
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);

// 6. إعادة التوجيه إلى الفاتورة
header("Location: invoice.php?id=" . $order_id);
exit;
?>