<?php
session_start();
require_once 'includes/db.connection.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $errors = [];

    // validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($password)) {
        $errors[] = "Password cannot be empty.";
    }

    if (count($errors) === 0) {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare('SELECT id, password, role FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            // bind result
            $stmt->bind_result($id, $hashed_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {

    $_SESSION['user_id'] = $id;
    $_SESSION['user_role'] = $role;
    $_SESSION['user_email'] = $email;

    if ($role === 'admin') {
        header("Location:../php-project/admin/admindashboard.php");
    } else {
        header("Location: ../php-project/index/index.php");
    }
    exit();
}

            } else {
                $errors[] = "Invalid email or password.";
            }
        } else {
            $errors[] = "No account found with that email.";
        }

        $stmt->close();
        $conn->close();
    }

  
    if (!empty($errors)) {
        foreach ($errors as $err) {
            echo $err . "<br>";
        }
    }

?>
