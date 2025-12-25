<?php
session_start();
require_once __DIR__ . '/../includes/db.connection.php';
$db = Database::getInstance();
$conn = $db->getConnection();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$db = Database::getInstance();
$conn = $db->getConnection();

$profile_success = $profile_error = $password_success = 
. assword_error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone_number = trim($_POST['phone_number'] ?? '');
        $date_of_birth = $_POST['date_of_birth'] ?? null;

        if ($name === '' || $email === '') {
            $profile_error = "Name and Email are required.";
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone_number = ?, date_of_birth = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $email, $phone_number, $date_of_birth, $user_id);
            if ($stmt->execute()) {
                $profile_success = "Profile updated successfully.";
            } else {
                $profile_error = "Failed to update profile.";
            }
        }
    }

    if ($_POST['action'] === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($new_password !== $confirm_password) {
            $password_error = "New passwords do not match.";
        } elseif (strlen($new_password) < 8) {
            $password_error = "Password must be at least 8 characters.";
        } else {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if (!$row || !password_verify($current_password, $row['password'])) {
                $password_error = "Current password is incorrect.";
            } else {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update->bind_param("si", $hashed, $user_id);
                if ($update->execute()) {
                    $password_success = "Password updated successfully.";
                } else {
                    $password_error = "Failed to update password.";
                }
            }
        }
    }
}


$user_stmt = $conn->prepare("SELECT name, email, phone_number, date_of_birth FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();


$orders_stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders = $orders_stmt->get_result();

include __DIR__ . '/profile.view.php';
?>