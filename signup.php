<!-- signup.php -->
 
<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up - Sarthi Travels</title>
    <script>
        function reloadCaptcha() {
            fetch('generate_captcha.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('captchaDisplay').innerText = data;
                });
        }

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

        window.onload = reloadCaptcha;
    </script>
</head>
<body>
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
        <span id="captchaDisplay" style="font-weight:bold; font-size: 20px; color:blue;"></span>
        <button type="button" onclick="reloadCaptcha()">Reload</button><br><br>
        <input type="text" name="captcha_input" required placeholder="Enter above code"><br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
