<!-- reset_password.php -->

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
