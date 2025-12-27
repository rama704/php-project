<?php
session_start();
require_once __DIR__ . '/includes/db.connection.php';

$db = Database::getInstance();
$conn = $db->getConnection();

$user_id = $_SESSION['user_id'] ?? null;
$session_id = session_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities']) && is_array($_POST['quantities'])) {
    // تحديد نوع الحذف: حسب user_id أو session_id
    if ($user_id) {
        $sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
    } else {
        $sql = "UPDATE cart SET quantity = ? WHERE id = ? AND session_id = ?";
    }

    $stmt = $conn->prepare($sql);

    foreach ($_POST['quantities'] as $cart_id => $quantity) {
        $quantity = (int) $quantity;
        if ($quantity <= 0) {
            // إذا كانت الكمية ≤ 0، نحذف العنصر
            removeCartItem($conn, $cart_id, $user_id, $session_id);
            continue;
        }

        if ($user_id) {
            $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
        } else {
            $stmt->bind_param("iis", $quantity, $cart_id, $session_id);
        }
        $stmt->execute();
    }
}

$conn->close();

// إعادة التوجيه لـ cart.php بعد التحديث
header("Location: cart.php");
exit;

// دالة منفصلة لحذف عنصر
function removeCartItem($conn, $cart_id, $user_id, $session_id) {
    if ($user_id) {
        $sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $cart_id, $user_id);
    } else {
        $sql = "DELETE FROM cart WHERE id = ? AND session_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $cart_id, $session_id);
    }
    $stmt->execute();
}
?>