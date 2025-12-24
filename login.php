<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Real Estate</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #000;
            color: #fff;
            min-height: 100vh;
            display: flex;
        }
        .container { display: flex; width: 100%; min-height: 100vh; }
        .left-panel {
            flex: 1;
            background: #000;
            padding: 60px 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 500px;
            position: relative;
        }
        .logo { position: absolute; top: 40px; left: 80px; font-size: 24px; letter-spacing: 2px; }
        h1 { font-size: 48px; font-weight: 300; margin-bottom: 60px; }
        .form-group { margin-bottom: 25px; }
        label {
            display: block;
            font-size: 11px;
            margin-bottom: 8px;
            color: rgba(255,255,255,0.5);
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 400;
        }
        .input-wrapper { position: relative; }
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.4);
            font-size: 16px;
        }
        input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            background: transparent;
            border: 1px solid #2a2a2a;
            border-radius: 4px;
            color: #fff;
            font-size: 14px;
            transition: all 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #4263eb;
            box-shadow: 0 0 0 1px #4263eb;
        }
        input::placeholder { color: rgba(255,255,255,0.3); }
        .sign-in-btn {
            width: 100%;
            padding: 16px;
            background: #4263eb;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            margin-top: 30px;
            font-weight: 500;
        }
        .sign-in-btn:hover {
            background: #3451d1;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(66, 99, 235, 0.3);
        }
        .signup-link { margin-top: 40px; font-size: 13px; color: rgba(255,255,255,0.4); }
        .signup-link a { color: #fff; text-decoration: none; margin-left: 5px; }
        .signup-link a:hover { color: #4263eb; }
        .right-panel {
            flex: 1;
            background: url("images/png.avif") center/cover no-repeat;
            position: relative;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="left-panel">
        <div class="logo">ESTATE</div>
        <h1>Sign In</h1>

        <!-- الفورم مرتبط بـ PHP -->
        <form action="login2.php" method="POST" id="signinForm">

            <div class="form-group">
                <label>Email</label>
                <div class="input-wrapper">
                    <span class="input-icon">📧</span>
                    <input type="email" name="email" placeholder="Enter Your Email" required>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input type="password" name="password" placeholder="Enter Your Password" required>
                </div>
            </div>

            <button type="submit" class="sign-in-btn">Sign In</button>
        </form>

        <div class="signup-link">
            Don't have an account? <a href="signup.html">Sign Up</a>
        </div>
    </div>

    <div class="right-panel"></div>
</div>

</body>
</html>
