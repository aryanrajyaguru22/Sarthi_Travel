<!-- index.php -->

<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Sarthi Travels</title>
    <script>
        function reloadCaptcha() {
            fetch('generate_captcha.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('captchaDisplay').innerText = data;
                });
        }

        window.onload = reloadCaptcha;
    </script>
</head>
<body>
    <h2>Welcome to Sarthi Travels</h2>
    <form method="POST" action="login_action.php">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Captcha:</label><br>
        <span id="captchaDisplay" style="font-weight:bold; font-size: 20px; color:blue;"></span>
        <button type="button" onclick="reloadCaptcha()">Reload</button><br><br>
        <input type="text" name="captcha_input" required placeholder="Enter above code"><br><br>

        <button type="submit">Login</button><br><br>
        <a href="signup.php">Sign Up</a> | <a href="forgot_password.php">Forgot Password</a>
    </form>
</body>
</html>
