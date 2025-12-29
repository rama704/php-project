<?php
session_start();
require_once __DIR__ . '/includes/db.connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

    if ($product_id <= 0 || $quantity <= 0) {
        die("Invalid product or quantity");
    }

    $db = Database::getInstance();
    $conn = $db->getConnection();

   
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $session_id = session_id();

    if ($user_id) {
        $sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $product_id);
    } else {
        $sql = "SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $session_id, $product_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_item = $result->fetch_assoc();

    if ($existing_item) {
       
        $new_quantity = $existing_item['quantity'] + $quantity;
        $update_sql = "UPDATE cart SET quantity = ?, created_at = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $new_quantity, $existing_item['id']);
        $update_stmt->execute();
    } else {
        
        if ($user_id) {
            $insert_sql = "INSERT INTO cart (user_id, product_id, quantity, created_at) VALUES (?, ?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
        } else {
            $insert_sql = "INSERT INTO cart (session_id, product_id, quantity, created_at) VALUES (?, ?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("sii", $session_id, $product_id, $quantity);
        }
        $insert_stmt->execute();
    }

    $conn->close();

    
    header("Location: product_details.php?id=" . $product_id);
    exit;
}
?>
