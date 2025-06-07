<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sarthi Travels</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h2 {
            text-align: center;
            color: #4CAF50;
            font-size: 36px;
            margin-top: 50px;
            animation: fadeIn 1s ease-out;
        }

        .form-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-out;
        }

        input[type="email"], input[type="password"], input[type="text"], button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        input[type="email"]:focus, input[type="password"]:focus, input[type="text"]:focus {
            border-color: #4CAF50;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        #captchaDisplay {
            font-weight: bold;
            font-size: 20px;
            color: #4CAF50;
            display: inline-block;
            margin-right: 10px;
            animation: fadeIn 1s ease-out;
        }

        .reload-btn {
            background-color: #f44336;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            font-size: 14px;
        }

        .reload-btn:hover {
            background-color: #e53935;
        }

        .form-links {
            text-align: center;
            margin-top: 15px;
        }

        .form-links a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 16px;
        }

        .form-links a:hover {
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @media (max-width: 480px) {
            .form-container {
                width: 90%;
                margin: 20px auto;
            }

            h2 {
                font-size: 28px;
            }
        }
    </style>
    <script>
        function reloadCaptcha() {
            fetch('generate_captcha.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('captchaDisplay').innerText = data;
                });
        }

        // Onload, load captcha
        window.onload = reloadCaptcha;
    </script>
</head>
<body>

<div class="form-container">
    <h2>સ્વાગત છે તમારું સારથી ટ્રાવેલ્સ </h2>
    <form method="POST" action="login_action.php">
        <label>ઇમેઇલ:</label><br>
        <input type="email" name="email" required placeholder="તમારું ઇમેઇલ અહીંયા લખો"><br><br>

        <label>પાસવર્ડ:</label><br>
        <input type="password" name="password" required placeholder="તમારું પાસવર્ડ અહીંયા લખો"><br><br>

        <label>Captcha:</label><br>
        <span id="captchaDisplay"></span>
        <button type="button" class="reload-btn" onclick="reloadCaptcha()">ફરી જાણો કોડે </button><br><br>

        <input type="text" name="captcha_input" required placeholder="ઉપર આપે લો કોડે અહીંયા લખો"><br><br>

        <button type="submit">Login</button><br><br>

        <div class="form-links">
            <a href="signup.php">નવું એકાઉન્ટ બનાવો </a> | <a href="forgot_password.php">ફરી જાણો પાસવર્ડ</a>
        </div>
    </form>
</div>

</body>
</html>
