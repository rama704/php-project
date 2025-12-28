<?php
require_once 'admin_auth.php';
require_once "../includes/db.connection.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['review_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$review_id = $_POST['review_id'];

if ($review_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid review ID']);
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

$stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
$stmt->bind_param("i", $review_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>