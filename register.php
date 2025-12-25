<?php
if(!empty($errors)){
    echo '<div class="error-messages">';
    foreach($errors as $error){
        echo "<p>$error</p>";
    }
    echo '</div>';
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Real Estate</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #000; color: #fff; min-height: 100vh; display: flex; }
        .container { display: flex; width: 100%; min-height: 100vh; }
        .left-panel { flex: 1; background: rgba(0,0,0,0.85); padding: 60px 80px; display: flex; flex-direction: column; justify-content: center; max-width: 500px; position: relative; }
        .logo { position: absolute; top: 40px; left: 80px; font-size: 24px; letter-spacing: 2px; }
        h1 { font-size: 48px; font-weight: 300; margin-bottom: 60px; }
        .form-group { margin-bottom: 25px; position: relative; }
        label { font-size: 11px; margin-bottom: 8px; display: block; color: rgba(255,255,255,0.6); text-transform: uppercase; }
        input { width: 100%; padding: 15px; background: rgba(255,255,255,0.05); border: 1px solid #2a2a2a; color: #fff; border-radius: 4px; }
        .error-msg { color: #ff4d4f; font-size: 0.85em; margin-top: 5px; }
        .sign-up-btn { width: 100%; padding: 16px; background: linear-gradient(135deg, #4263eb, #5f7cff); border: none; color: #fff; margin-top: 30px; cursor: pointer; }
        .right-panel { flex: 1; background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.85)), url("images/png.avif"); background-size: cover; background-position: center; }
    </style>
</head>
<body>

<div class="container">

    <div class="left-panel">
        <div class="logo">ESTATE</div>
        <h1>Sign Up</h1>

        <!-- ✅ FORM -->
        <form action="signup.php" method="POST" id="signupForm">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" id="name" required>
                <div class="error-msg" id="nameError"></div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="email" required>
                <div class="error-msg" id="emailError"></div>
            </div>

            <div class="form-group">
                <label>Mobile</label>
                <input type="tel" name="phone_number" id="phone_number" required>
                <div class="error-msg" id="mobileError"></div>
            </div>

            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" id="dob" required>
                <div class="error-msg" id="dobError"></div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" id="password" required>
                <div class="error-msg" id="passwordError"></div>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" id="confirmPassword" required>
                <div class="error-msg" id="confirmPasswordError"></div>
            </div>

            <button class="sign-up-btn" type="submit">Sign Up</button>
        </form>
    </div>

    <div class="right-panel"></div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("signupForm");

    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirmPassword");
    const mobileInput = document.getElementById("phone_number");
    const dobInput = document.getElementById("dob");

    const nameError = document.getElementById("nameError");
    const emailError = document.getElementById("emailError");
    const passwordError = document.getElementById("passwordError");
    const confirmPasswordError = document.getElementById("confirmPasswordError");
    const mobileError = document.getElementById("mobileError");
    const dobError = document.getElementById("dobError");

    // Full Name validation
    nameInput.addEventListener("input", () => {
        const words = nameInput.value.trim().split(" ");
        nameError.textContent = (words.length !== 4) ? "Full name must contain 4 words" : "";
    });

    // Email validation
    emailInput.addEventListener("input", () => {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        emailError.textContent = regex.test(emailInput.value) ? "" : "Invalid email format";
    });

    // Password validation
    passwordInput.addEventListener("input", () => {
        passwordError.textContent = (passwordInput.value.length < 6) ? "Password must be at least 6 characters" : "";
    });

    // Confirm Password validation
    confirmPasswordInput.addEventListener("input", () => {
        confirmPasswordError.textContent = (confirmPasswordInput.value !== passwordInput.value) ? "Passwords do not match" : "";
    });

    // Mobile validation
    mobileInput.addEventListener("input", () => {
        const regex = /^[0-9]{10}$/;
        mobileError.textContent = regex.test(mobileInput.value) ? "" : "Mobile number must be exactly 10 digits";
    });

    // DOB validation
    dobInput.addEventListener("input", () => {
        if (!dobInput.value) {
            dobError.textContent = "Date of birth is required";
            return;
        }
        const dobDate = new Date(dobInput.value);
        const today = new Date();
        const age = today.getFullYear() - dobDate.getFullYear();
        dobError.textContent = (age < 16) ? "You must be at least 16 years old" : "";
    });

    // Prevent submit if any errors
    form.addEventListener("submit", (e) => {
        if (nameError.textContent || emailError.textContent || passwordError.textContent || confirmPasswordError.textContent || mobileError.textContent || dobError.textContent) {
            e.preventDefault();
            alert("Please fix the errors before submitting.");
        }
    });
});
</script>

</body>
</html>
