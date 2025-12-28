<?php 
require_once 'includes/db.connection.php';
 $db = Database::getInstance();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password_confirmation = trim($_POST['confirm_password']);
    $mobile = trim($_POST['phone_number']);
    $dob = trim($_POST['date_of_birth']);
    $errors = [];

    if ($password !== $password_confirmation) {
         $errors[] = "Passwords do not match.";
    }
    if(!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) { 
        $errors[] = "Invalid email format";
    }
    if(!preg_match("/^[0-9]{10}$/", trim($mobile))) {
        $errors[] = "Mobile number must be exactly 10 digits";
    }
    if(count(explode(" ", trim($name))) !== 4) { 
        $errors[] = "Full name must contain 4 words";
    }
    $dob_date = new DateTime($dob);
      $today = new DateTime();
       $age = $today->diff($dob_date)->y;
     if($age < 16) {
      $errors[] = "You must be at least 16 years old";
}

   
    $conn = $db->getConnection();
    $checkEmailStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();
    if($checkEmailStmt->num_rows > 0) {
   
    $errors[] = "This email is already registered. Please use a different email.";

}
   $checkEmailStmt->close();

     if(count($errors) === 0) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    
    $role = 'user';
     $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, phone_number, date_of_birth) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $hashed_password, $role, $mobile, $dob);

    if($stmt->execute()) {
        header("Location: login.php?signup=success");
        exit;
    } else {
        $errors[] = "Error during registration. Please try again.";
    }


   if(isset($stmt)){
    $stmt->close();
}
$conn->close();
}}
?>