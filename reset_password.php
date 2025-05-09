<?php
session_start();
include 'db.php';

// Get form data
$email = $_POST['email'];
$new_password = $_POST['new_password'];
$captcha_input = $_POST['captcha_input'];

// Captcha check
if (strtolower($captcha_input) !== strtolower($_SESSION['captcha'])) {
    echo "<script>alert('Captcha incorrect.'); window.location='forgot_password.php';</script>";
    exit();
}

// Check if email exists in the database
$check = $conn->query("SELECT * FROM users WHERE email='$email'");
if ($check->num_rows == 0) {
    echo "<script>alert('Email not found.'); window.location='forgot_password.php';</script>";
    exit();
}

// Password validation function
function validatePassword($pass) {
    return strlen($pass) >= 8 &&
           preg_match_all('/[a-zA-Z]/', $pass, $m) && count($m[0]) >= 3 &&
           preg_match('/[A-Z]/', $pass) &&
           preg_match('/[a-z]/', $pass) &&
           preg_match('/[^a-zA-Z0-9]/', $pass);
}

// Validate new password
if (!validatePassword($new_password)) {
    echo "<script>alert('Password does not meet required rules.'); window.location='forgot_password.php';</script>";
    exit();
}

// Hash the new password
$hashed = password_hash($new_password, PASSWORD_DEFAULT);

// Update password in the database
$conn->query("UPDATE users SET password='$hashed' WHERE email='$email'");
echo "<script>alert('Password updated successfully. You can now log in.'); window.location='index.php';</script>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sarthi Travels</title>
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

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
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
    <h2>Reset Your Password</h2>
    <form method="POST" action="reset_password.php">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>New Password:</label><br>
        <input type="password" name="new_password" required><br><br>

        <label>Captcha:</label><br>
        <span id="captchaDisplay"></span>
        <button type="button" class="reload-btn" onclick="reloadCaptcha()">Reload</button><br><br>

        <input type="text" name="captcha_input" required placeholder="Enter above code"><br><br>

        <button type="submit">Reset Password</button><br><br>

        <p class="form-links">Got Login? <a href="index.php">Login here</a></p>
    </form>
</div>

</body>
</html>
