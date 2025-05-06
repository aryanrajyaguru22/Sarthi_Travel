<!-- forgot_password.php -->

<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head><title>Forgot Password</title></head>
<body>
    <h2>Reset Password</h2>
    <form method="POST" action="reset_password.php" onsubmit="return validateCaptcha()">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Captcha:</label><br>
        <span id="captchaDisplay" style="font-weight:bold; font-size: 20px; color:blue;"></span>
        <button type="button" onclick="reloadCaptcha()">Reload</button><br><br>
        <input type="text" name="captcha_input" required><br><br>

        <button type="submit">Reset Password</button>
    </form>

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
</body>
</html>
