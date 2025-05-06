<!-- signup_action.php -->

<?php
session_start();
include 'db.php'; // Your database connection

$full_name = $_POST['full_name'];
$email = $_POST['email'];
$mobile = $_POST['mobile'];
$password = $_POST['password'];
$captcha_input = $_POST['captcha_input'];

// Captcha check
if (strtolower($captcha_input) !== strtolower($_SESSION['captcha'])) {
    echo "<script>alert('Captcha incorrect.'); window.location='signup.php';</script>";
    exit();
}

// Email exists check
$result = $conn->query("SELECT * FROM users WHERE email='$email'");
if ($result->num_rows > 0) {
    echo "<script>alert('Email is already registered.'); window.location='signup.php';</script>";
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

if (!validatePassword($password)) {
    echo "<script>alert('Password does not meet the required rules.'); window.location='signup.php';</script>";
    exit();
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user into database
$sql = "INSERT INTO users (full_name, email, mobile, password) VALUES ('$full_name', '$email', '$mobile', '$hashed_password')";
if ($conn->query($sql)) {
    echo "<script>alert('Registration successful.'); window.location='index.php';</script>";
} else {
    echo "<script>alert('Error occurred.'); window.location='signup.php';</script>";
}
?>
