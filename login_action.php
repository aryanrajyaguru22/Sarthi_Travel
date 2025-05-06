<!-- login_action.php -->

<?php
session_start();
include 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];
$captcha_input = $_POST['captcha_input'];

// Captcha check
if (strtolower($captcha_input) !== strtolower($_SESSION['captcha'])) {
    echo "<script>alert('Captcha incorrect.'); window.location='index.php';</script>";
    exit();
}

$result = $conn->query("SELECT * FROM users WHERE email='$email'");
if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['full_name'];
        echo "<script>alert('Login successful. Welcome {$user['full_name']}'); window.location='dashboard.php';</script>";
        exit();
    }
}

echo "<script>alert('Invalid email or password.'); window.location='index.php';</script>";
?>
