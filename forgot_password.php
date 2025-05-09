<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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

        input[type="email"], input[type="text"], button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        input[type="email"]:focus, input[type="text"]:focus {
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

        @media (max-width: 480px) {
            .form-container {
                width: 90%;
                margin: 20px auto;
            }

            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Reset Password</h2>
    <form method="POST" action="reset_password.php" onsubmit="return validateCaptcha()">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Captcha:</label><br>
        <span id="captchaDisplay"></span>
        <button type="button" class="reload-btn" onclick="reloadCaptcha()">Reload</button><br><br>
        <input type="text" name="captcha_input" required><br><br>

        <button type="submit">Reset Password</button>
    </form>
</div>

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

    // Function to validate captcha (basic example)
    function validateCaptcha() {
        const captchaInput = document.querySelector('[name="captcha_input"]');
        const captcha = document.getElementById('captchaDisplay').innerText.trim();
        if (captchaInput.value.trim() !== captcha) {
            alert('Captcha does not match!');
            return false;
        }
        return true;
    }
</script>

</body>
</html>
