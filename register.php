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
    <title>Sign Up - Techify</title>
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
            position: relative;
        }
        
        /* Animated Background Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }
        
        .particle {
            position: absolute;
            background: rgba(254, 164, 11, 0.3);
            border-radius: 50%;
            animation: float 20s infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); opacity: 0; }
            10% { opacity: 0.5; }
            90% { opacity: 0.5; }
            100% { transform: translateY(-100vh) translateX(50px); opacity: 0; }
        }
        
        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #000000 0%, #0a0a0a 50%, #000000 100%);
            padding: 60px 80px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            max-width: 500px;
            position: relative;
            z-index: 1;
            box-shadow: 5px 0 30px rgba(0, 0, 0, 0.5);
            overflow-y: auto;
            padding-top: 120px;
            padding-bottom: 60px;
        }
        
        /* Custom Scrollbar */
        .left-panel::-webkit-scrollbar {
            width: 8px;
        }
        
        .left-panel::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .left-panel::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #fea40b, #84641c);
            border-radius: 4px;
        }
        
        .left-panel::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #84641c, #fea40b);
        }
        
        /* Glow Effect */
        .left-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 50%, rgba(254, 164, 11, 0.1), transparent 50%);
            pointer-events: none;
        }
        
        .logo { 
            position: absolute; 
            top: 40px; 
            left: 80px; 
            font-size: 28px; 
            letter-spacing: 3px;
            font-weight: 700;
            background: linear-gradient(135deg, #fea40b, #84641c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: glow 3s ease-in-out infinite;
        }
        
        @keyframes glow {
            0%, 100% { filter: drop-shadow(0 0 10px rgba(254, 164, 11, 0.5)); }
            50% { filter: drop-shadow(0 0 20px rgba(254, 164, 11, 0.8)); }
        }
        
        h1 { 
            font-size: 56px; 
            font-weight: 300; 
            margin-bottom: 35px;
            margin-top: 80px;
            animation: slideDown 0.8s ease-out;
            background: linear-gradient(135deg, #fff, #a0a0a0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-group { 
            margin-bottom: 12px;
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }
        
        .form-group:nth-child(1) { animation-delay: 0.2s; }
        .form-group:nth-child(2) { animation-delay: 0.25s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.35s; }
        .form-group:nth-child(5) { animation-delay: 0.4s; }
        .form-group:nth-child(6) { animation-delay: 0.45s; }
        
        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
            from { opacity: 0; transform: translateY(20px); }
        }
        
        label {
            display: block;
            font-size: 11px;
            margin-bottom: 8px;
            color: rgba(255,255,255,0.6);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-weight: 500;
        }
        
        .input-wrapper { 
            position: relative;
            overflow: hidden;
        }
        
        .input-wrapper::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #fea40b, transparent);
            transition: all 0.5s ease;
            transform: translateX(-50%);
        }
        
        .input-wrapper:focus-within::after {
            width: 100%;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            transition: all 0.3s ease;
            filter: grayscale(1);
        }
        
        .input-wrapper:focus-within .input-icon {
            filter: grayscale(0);
            transform: translateY(-50%) scale(1.2);
        }
        
        input {
            width: 100%;
            padding: 12px 15px 12px 50px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid #2a2a2a;
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            transition: all 0.4s ease;
            backdrop-filter: blur(10px);
        }
        
        input:focus {
            outline: none;
            border-color: #fea40b;
            background: rgba(254, 164, 11, 0.05);
            box-shadow: 0 0 0 1px #fea40b, 0 8px 25px rgba(254, 164, 11, 0.2);
            transform: translateY(-2px);
        }
        
        input::placeholder { 
            color: rgba(255,255,255,0.3); 
        }

        .error-msg { 
            color: #ff4d4f; 
            font-size: 0.75em; 
            margin-top: 3px;
            margin-right: 50px;
            min-height: 14px;
        }
        
        .sign-up-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #fea40b 0%, #84641c 100%);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            margin-top: 25px;
            margin-bottom: 30px;
            font-weight: 600;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(254, 164, 11, 0.3);
            animation: fadeInUp 0.6s ease-out 0.5s forwards;
            opacity: 0;
        }
        
        .sign-up-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transition: width 0.6s, height 0.6s, top 0.6s, left 0.6s;
            transform: translate(-50%, -50%);
        }
        
        .sign-up-btn:hover::before {
            width: 400px;
            height: 400px;
        }
        
        .sign-up-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(254, 164, 11, 0.5);
        }
        
        .sign-up-btn:active {
            transform: translateY(-1px);
        }
        
        .signin-link { 
            margin-top: 20px; 
            margin-bottom: 40px;
            font-size: 13px; 
            color: rgba(255,255,255,0.5);
            text-align: center;
            animation: fadeInUp 0.6s ease-out 0.6s forwards;
            opacity: 0;
        }
        
        .signin-link a { 
            color: #fff; 
            text-decoration: none; 
            margin-left: 5px;
            font-weight: 600;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .signin-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #fea40b, #84641c);
            transition: width 0.3s ease;
        }
        
        .signin-link a:hover {
            color: #fea40b;
        }
        
        .signin-link a:hover::after {
            width: 100%;
        }
        
        .right-panel {
            flex: 1;
            background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Animated gradient background */
        .right-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 30% 50%, rgba(254, 164, 11, 0.15), transparent 50%),
                radial-gradient(circle at 70% 80%, rgba(132, 100, 28, 0.1), transparent 50%);
            animation: rotateGradient 20s linear infinite;
        }
        
        @keyframes rotateGradient {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Animated overlay pattern */
        .right-panel::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(45deg, transparent 30%, rgba(254, 164, 11, 0.05) 30%, rgba(254, 164, 11, 0.05) 70%, transparent 70%);
            background-size: 100px 100px;
            animation: movePattern 20s linear infinite;
        }
        
        @keyframes movePattern {
            0% { background-position: 0 0; }
            100% { background-position: 100px 100px; }
        }
        
        /* 3D Device Mockup Container */
        .device-showcase {
            position: relative;
            z-index: 2;
            animation: floatDevice 6s ease-in-out infinite;
            transform-style: preserve-3d;
            perspective: 1000px;
        }
        
        @keyframes floatDevice {
            0%, 100% { transform: translateY(0px) rotateY(0deg); }
            25% { transform: translateY(-20px) rotateY(5deg); }
            50% { transform: translateY(0px) rotateY(0deg); }
            75% { transform: translateY(-15px) rotateY(-5deg); }
        }
        
        /* Main Device */
        .device-mockup {
            width: 600px;
            height: 400px;
            background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
            border-radius: 30px;
            padding: 15px;
            box-shadow: 
                0 50px 100px rgba(0, 0, 0, 0.5),
                0 0 100px rgba(254, 164, 11, 0.2),
                inset 0 0 50px rgba(254, 164, 11, 0.05);
            position: relative;
            transform: rotateX(5deg) rotateY(-10deg);
            transition: transform 0.3s ease;
        }
        
        .device-mockup:hover {
            transform: rotateX(0deg) rotateY(0deg) scale(1.05);
        }
        
        /* Screen */
        .device-screen {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0a0a0a, #1a1a1a);
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 0 30px rgba(0, 0, 0, 0.8);
        }
        
        /* Animated content inside screen */
        .screen-content {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 30px;
            padding: 40px;
        }
        
        /* Animated Icons */
        .tech-icons {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .tech-icon {
            font-size: 60px;
            animation: bounce 2s ease-in-out infinite;
            filter: drop-shadow(0 5px 15px rgba(254, 164, 11, 0.5));
        }
        
        .tech-icon:nth-child(1) { animation-delay: 0s; }
        .tech-icon:nth-child(2) { animation-delay: 0.2s; }
        .tech-icon:nth-child(3) { animation-delay: 0.4s; }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        
        /* Glowing text */
        .screen-text {
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #fea40b, #84641c, #fea40b);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 3s ease infinite;
            text-align: center;
            line-height: 1.4;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        /* Floating circles decoration */
        .floating-circle {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(254, 164, 11, 0.2), transparent);
            animation: floatCircle 15s ease-in-out infinite;
        }
        
        .circle-1 {
            width: 200px;
            height: 200px;
            top: 10%;
            right: 10%;
            animation-delay: 0s;
        }
        
        .circle-2 {
            width: 150px;
            height: 150px;
            bottom: 15%;
            left: 15%;
            animation-delay: 3s;
        }
        
        .circle-3 {
            width: 100px;
            height: 100px;
            top: 50%;
            right: 25%;
            animation-delay: 6s;
        }
        
        @keyframes floatCircle {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.3; }
            25% { transform: translate(20px, -20px) scale(1.1); opacity: 0.5; }
            50% { transform: translate(-15px, 15px) scale(0.9); opacity: 0.4; }
            75% { transform: translate(-20px, -10px) scale(1.05); opacity: 0.6; }
        }
        
        /* Responsive Design */
        @media (max-width: 968px) {
            .container { flex-direction: column; }
            .left-panel {
                max-width: 100%;
                padding: 40px;
                min-height: 100vh;
            }
            .right-panel { display: none; }
            .logo { left: 40px; top: 30px; }
            h1 { font-size: 42px; margin-top: 60px; }
        }
        
        @media (max-width: 576px) {
            .left-panel { padding: 30px 20px; }
            .logo { left: 20px; top: 20px; font-size: 22px; }
            h1 { font-size: 36px; margin-bottom: 40px; margin-top: 50px; }
        }
    </style>
</head>
<body>

<!-- Animated Background Particles -->
<div class="particles" id="particles"></div>

<div class="container">
    <div class="left-panel">
        <div class="logo">techify</div>
        <h1>Sign Up</h1>

        <!-- ✅ FORM -->
        <form action="signup.php" method="POST" id="signupForm">

            <div class="form-group">
                <label>Full Name</label>
                <div class="input-wrapper">
                    <span class="input-icon">👤</span>
                    <input type="text" name="name" id="name" placeholder="Enter Your Full Name" required>
                </div>
                <div class="error-msg" id="nameError"></div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <div class="input-wrapper">
                    <span class="input-icon">📧</span>
                    <input type="email" name="email" id="email" placeholder="Enter Your Email" required>
                </div>
                <div class="error-msg" id="emailError"></div>
            </div>

            <div class="form-group">
                <label>Mobile</label>
                <div class="input-wrapper">
                    <span class="input-icon">📱</span>
                    <input type="tel" name="phone_number" id="phone_number" placeholder="Enter Your Mobile Number" required>
                </div>
                <div class="error-msg" id="mobileError"></div>
            </div>

            <div class="form-group">
                <label>Date of Birth</label>
                <div class="input-wrapper">
                    <span class="input-icon">📅</span>
                    <input type="date" name="date_of_birth" id="dob" required>
                </div>
                <div class="error-msg" id="dobError"></div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input type="password" name="password" id="password" placeholder="Enter Your Password" required>
                </div>
                <div class="error-msg" id="passwordError"></div>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">🔐</span>
                    <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm Your Password" required>
                </div>
                <div class="error-msg" id="confirmPasswordError"></div>
            </div>

            <button class="sign-up-btn" type="submit">Sign Up</button>
        </form>

        <div class="signin-link">
            Already have an account? <a href="login.php">Sign In</a>
        </div>
    </div>

    <div class="right-panel">
        <!-- Floating circles decoration -->
        <div class="floating-circle circle-1"></div>
        <div class="floating-circle circle-2"></div>
        <div class="floating-circle circle-3"></div>
        
        <!-- 3D Device Mockup -->
        <div class="device-showcase">
            <div class="device-mockup">
                <div class="device-screen">
                    <div class="screen-content">
                        <div class="tech-icons">
                            <div class="tech-icon">💻</div>
                            <div class="tech-icon">🎧</div>
                            <div class="tech-icon">📱</div>
                        </div>
                        <div class="screen-text">
                            Join<br>techify shop
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Create animated particles
    const particlesContainer = document.getElementById('particles');
    const particleCount = 15;
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        // Random size between 2-6px
        const size = Math.random() * 4 + 2;
        particle.style.width = size + 'px';
        particle.style.height = size + 'px';
        
        // Random starting position
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        
        // Random animation delay
        particle.style.animationDelay = Math.random() * 20 + 's';
        particle.style.animationDuration = (Math.random() * 10 + 15) + 's';
        
        particlesContainer.appendChild(particle);
    }

    // Form validation
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
            } else {
                const btn = form.querySelector('.sign-up-btn');
                btn.innerHTML = '<span style="display: inline-block; animation: spin 1s linear infinite;">⏳</span> Processing...';
                btn.style.pointerEvents = 'none';
            }
        });
    });
    
    // Spin animation for loading icon
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
</script>

</body>
</html>