<?php
session_start();
require_once __DIR__ . '/includes/db.connection.php';

$db = Database::getInstance();
$conn = $db->getConnection();

$user_id = $_SESSION['user_id'] ?? null;
$session_id = session_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = (int)$_POST['cart_id'];

    if ($user_id) {
        $sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $cart_id, $user_id);
    } else {
        $sql = "DELETE FROM cart WHERE id = ? AND session_id = ?";
        $stmt->bind_param("is", $cart_id, $session_id);
    }

    $stmt->execute();
}

$conn->close();
header("Location: cart.php");
exit;
?>