<?php
include 'db.php';
session_start();

// Get ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid bus ID.'); window.location='add_bus.php';</script>";
    exit();
}
$id = intval($_GET['id']);

// Fetch existing data
$bus = $conn->query("SELECT * FROM buses WHERE id=$id")->fetch_assoc();
if (!$bus) {
    echo "<script>alert('એ નંબર ની બસ નથી લિસ્ટ માં '); window.location='add_bus.php';</script>";
    exit();
}

// Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_no = strtoupper(trim($_POST['bus_no']));
    $pattern = "/^[A-Z]{2}-\d{2}-[A-Z]{1}-\d{4}$/";

    if (!preg_match($pattern, $new_no)) {
        echo "<script>alert('બસ નંબર ફરીવાર નાખો જે રીતે કીધું છે. એ રીતે નાખો  GJ-18-A-0001');</script>";
    } else {
        // Check for duplicates (excluding current)
        $check = $conn->query("SELECT * FROM buses WHERE bus_no = '$new_no' AND id != $id");
        if ($check->num_rows > 0) {
            echo "<script>alert('એ બસ પેહેલે થી જ ઉમેરાઈ ગઈ છે ');</script>";
        } else {
            $conn->query("UPDATE buses SET bus_no='$new_no' WHERE id=$id");
            echo "<script>alert('બસ ની માહિતી માં ફેરફાર થઇ ગયો છે '); window.location='add_bus.php';</script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ફેરફાર કરો બસમાં - સારથી ટ્રાવેલ્સ</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            color: #4CAF50;
            margin-top: 20px;
        }
        .form-container {
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus {
            border-color: #4CAF50;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .alert {
            text-align: center;
            color: #e74c3c;
            font-size: 14px;
        }
        .form-container p {
            text-align: center;
            color: #999;
        }
    </style>
</head>
<body>

<h2>બસ નંબર ફેરફાર કરો</h2>
<div class="form-container">
    <form method="POST" onsubmit="return validateBusNo()">
        <label for="bus_no">અહીંયા બસ નંબર નાખો  (GJ-18-A-0001):</label>
        <input type="text" name="bus_no" id="bus_no" value="<?= htmlspecialchars($bus['bus_no']) ?>" required>
        <button type="submit">બસ માહિતી બદલવો </button>
        <p id="error-message" class="alert"></p>
    </form>
</div>

<script>
    const input = document.getElementById("bus_no");
    const errorMessage = document.getElementById("error-message");

    // Convert input to uppercase in real-time
    input.addEventListener("input", function() {
        this.value = this.value.toUpperCase();
        errorMessage.textContent = ''; // Clear previous error message
    });

    function validateBusNo() {
        const pattern = /^[A-Z]{2}-\d{2}-[A-Z]{1}-\d{4}$/;
        const value = input.value.trim();

        if (!pattern.test(value)) {
            errorMessage.textContent = "બસ નંબર ફરીવાર નાખો જે રીતે કીધું છે. એ રીતે નાખો  GJ-18-A-0001";
            return false;
        }

        return true;
    }
</script>

</body>
</html>
