<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: index.php");
    exit(); // Don't continue with the rest of the code
}

// Include the database connection
include 'db.php';

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id = '$user_id'");

// Check if user exists
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "<script>alert('User not found. Please log in again.'); window.location='login.php';</script>";
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile - Sarthi Travels</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .profile-container {
            width: 50%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        h2 {
            text-align: center;
        }

        .profile-info {
            margin: 15px 0;
        }

        .profile-info label {
            font-weight: bold;
        }

        .profile-info p {
            font-size: 16px;
            color: #333;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h2>User Profile</h2>
    <div class="profile-info">
        <label>Full Name:</label>
        <p><?= htmlspecialchars($user['full_name']) ?></p>
    </div>
    <div class="profile-info">
        <label>Email:</label>
        <p><?= htmlspecialchars($user['email']) ?></p>
    </div>
    <div class="profile-info">
        <label>Mobile Number:</label>
        <p><?= htmlspecialchars($user['mobile']) ?></p>
    </div>

    <a href="logout.php">Logout</a>
</div>

</body>
</html>
