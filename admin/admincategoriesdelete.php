<?php

require_once '../includes/db.connection.php';
require_once 'admin_auth.php';


$db = Database::getInstance();
$conn = $db->getConnection();

if (isset($_GET['id'])) {
    $category_id = $_GET['id'];
    
    $query = "DELETE FROM categories WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $category_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: admincategories.php?deleted=1");
exit;
?>