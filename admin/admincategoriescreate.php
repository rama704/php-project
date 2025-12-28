<?php
require_once '../includes/db.connection.php';
require_once 'admin_auth.php';

$db = Database::getInstance();
$conn = $db->getConnection();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $categoriesQuery="INSERT INTO categories(name,description) VALUES(?,?)";
    $stmt = mysqli_prepare($conn, $categoriesQuery);


    mysqli_stmt_bind_param($stmt, "ss", 
        $name,
        $description,
       
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: admincategories.php?created=1");
        exit;
    } else {
        $error = "Error: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <link rel="stylesheet" href="adminproductcreate.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>


<div class="container">
    <h2>Add New Category</h2>

    <?php if (isset($error)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Category Name:</label>
        <input type="text" name="name" placeholder="Enter category name" required>

        <label>Description:</label>
        <textarea name="description" placeholder="Enter category description" rows="4" required></textarea>

        <button type="submit">Add Category</button>
    </form>

</div>

</body>
</html>