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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #000;
            color: #fff;
            min-height: 100vh;
            display: flex;
        }

        .container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .left-panel {
            flex: 1;
            background: rgba(0,0,0,0.85);
            padding: 60px 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 500px;
            position: relative;
        }

        .logo {
            position: absolute;
            top: 40px;
            left: 80px;
            font-size: 24px;
            letter-spacing: 2px;
        }

        h1 {
            font-size: 48px;
            font-weight: 300;
            margin-bottom: 60px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            font-size: 11px;
            margin-bottom: 8px;
            display: block;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
        }

        input {
            width: 100%;
            padding: 15px;
            background: rgba(255,255,255,0.05);
            border: 1px solid #2a2a2a;
            color: #fff;
            border-radius: 4px;
        }

        .sign-up-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #4263eb, #5f7cff);
            border: none;
            color: #fff;
            margin-top: 30px;
            cursor: pointer;
        }

        .right-panel {
            flex: 1;
            background:
                linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.85)),
                url("images/png.avif");
            background-size: cover;
            background-position: center;
        }
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
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Mobile</label>
                <input type="tel" name="phone_number" required>
            </div>

            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" id="confirmPassword" required>
            </div>

            <button class="sign-up-btn" type="submit">Sign Up</button>
        </form>
    </div>

    <div class="right-panel"></div>
</div>

<script>
document.getElementById("signupForm").addEventListener("submit", function(e) {
    const password = document.getElementById("password").value;
    const confirm = document.getElementById("confirmPassword").value;

    if (password !== confirm) {
        e.preventDefault(); // ⛔ فقط عند الخطأ
        alert("Passwords do not match");
    }
});
</script>

</body>
</html>
