<?php
require_once 'admin_auth.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['order_id']) || !isset($_POST['status'])) {
    header("Location: orders.php");
    exit;
}

$order_id = (int) $_POST['order_id'];
$status = $_POST['status'];

// التحقق من القيم المسموحة في قاعدة البيانات (حسب عمود enum)
$allowed_statuses = ['pending', 'paid', 'shipped', 'failed']; // 
if (!in_array($status, $allowed_statuses)) {
    header("Location: order_items.php?id=" . $order_id);
    exit;
}

require_once '../includes/db.connection.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    header("Location: order_items.php?id=" . $order_id);
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
exit;
?>