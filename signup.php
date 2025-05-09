<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Sarthi Travels</title>
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

        input[type="text"], input[type="email"], input[type="password"], input[type="text"]:focus, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            border-color: #4CAF50;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 16px;
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
        // Captcha reloading function
        function reloadCaptcha() {
            fetch('generate_captcha.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('captchaDisplay').innerText = data;
                });
        }

        // Password validation
        function validatePassword() {
            const password = document.getElementById("password").value;
            const letters = password.match(/[a-zA-Z]/g);
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasSpecial = /[^A-Za-z0-9]/.test(password);

            if (password.length < 8 || !letters || letters.length < 3 || !hasUpper || !hasLower || !hasSpecial) {
                alert("Password must be 8+ characters, include at least 3 letters, 1 uppercase, 1 lowercase, and 1 special character.");
                return false;
            }
            return true;
        }

        // Onload, load captcha
        window.onload = reloadCaptcha;
    </script>
</head>
<body>

<div class="form-container">
    <h2>Sign Up - Sarthi Travels</h2>
    <form method="POST" action="signup_action.php" onsubmit="return validatePassword()">
        <label>Full Name:</label><br>
        <input type="text" name="full_name" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Mobile Number:</label><br>
        <input type="text" name="mobile" required><br><br>

        <label>Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label>Enter the code shown:</label><br>
        <span id="captchaDisplay"></span>
        <button type="button" class="reload-btn" onclick="reloadCaptcha()">Reload</button><br><br>
        <input type="text" name="captcha_input" required placeholder="Enter above code"><br><br>

        <button type="submit">Register</button>
    </form>

    <div class="form-links">
        <p>Already have an account? <a href="index.php">Login here</a></p>
    </div>
</div>

</body>
</html>
