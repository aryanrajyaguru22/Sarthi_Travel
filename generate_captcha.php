
<?php
session_start();

// Generate random alphanumeric code
$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$captcha_code = '';
for ($i = 0; $i < 6; $i++) {
    $captcha_code .= $chars[rand(0, strlen($chars) - 1)];
}
$_SESSION['captcha'] = $captcha_code;
echo $captcha_code;
?>
