<?php
session_start();
require_once __DIR__ . '/includes/db.connection.php'; // تأكد من مسار ملف الاتصال

$db = Database::getInstance();
$conn = $db->getConnection();

$user_id = $_SESSION['user_id'] ?? null; // استخدام null coalescing operator
$session_id = session_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = (int)$_POST['cart_id']; // تحويل القيمة إلى عدد صحيح لمنع SQL Injection

    if ($user_id) {
        // إذا كان المستخدم مسجل الدخول، استخدم user_id
        $sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $cart_id, $user_id);
            $stmt->execute();
            if ($stmt->affected_rows === 0) {
                // تم تنفيذ الاستعلام ولكن لم يتم حذف أي صف، ربما العنصر لم يكن ملك هذا المستخدم
                error_log("No cart item found for user_id: $user_id and cart_id: $cart_id");
            }
            $stmt->close();
        } else {
            error_log("Prepare failed for user deletion: " . $conn->error);
        }
    } else {
        // إذا لم يكن مسجل الدخول، استخدم session_id
        $sql = "DELETE FROM cart WHERE id = ? AND session_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("is", $cart_id, $session_id); // لاحظ 's' لـ session_id
            $stmt->execute();
            if ($stmt->affected_rows === 0) {
                // تم تنفيذ الاستعلام ولكن لم يتم حذف أي صف، ربما العنصر لم يكن ملك هذه الجلسة
                error_log("No cart item found for session_id: $session_id and cart_id: $cart_id");
            }
            $stmt->close();
        } else {
            error_log("Prepare failed for session deletion: " . $conn->error);
        }
    }
} else {
    // إذا لم يتم إرسال POST أو cart_id مفقود، إعادة التوجيه إلى السلة
    error_log("Invalid request to remove_item.php");
    header("Location: cart.php");
    exit;
}

$conn->close();

// إعادة التوجيه إلى صفحة السلة بعد الحذف
header("Location: cart.php");
exit;
?>