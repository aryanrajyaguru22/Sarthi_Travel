<!-- db.php -->
<?php
$host = "localhost";
$user = "root"; // or your DB username
$pass = "";     // or your DB password
$db = "travel_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
