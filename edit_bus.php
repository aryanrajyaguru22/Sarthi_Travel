<!-- edit_bus.php -->

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
    echo "<script>alert('Bus not found.'); window.location='add_bus.php';</script>";
    exit();
}

// Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_no = strtoupper(trim($_POST['bus_no']));
    $pattern = "/^[A-Z]{2}-\d{2}-[A-Z]{1}-\d{4}$/";

    if (!preg_match($pattern, $new_no)) {
        echo "<script>alert('Invalid Bus Number Format. Use GJ-18-A-0001');</script>";
    } else {
        // Check for duplicates (excluding current)
        $check = $conn->query("SELECT * FROM buses WHERE bus_no = '$new_no' AND id != $id");
        if ($check->num_rows > 0) {
            echo "<script>alert('Bus number already exists.');</script>";
        } else {
            $conn->query("UPDATE buses SET bus_no='$new_no' WHERE id=$id");
            echo "<script>alert('Bus updated successfully.'); window.location='add_bus.php';</script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Bus - Sarthi Travels</title>
</head>
<body>
    <h2>Edit Bus Number</h2>
    <form method="POST" onsubmit="return validateBusNo()">
        <label>Bus Number (Format: GJ-18-A-0001):</label><br>
        <input type="text" name="bus_no" id="bus_no" value="<?= htmlspecialchars($bus['bus_no']) ?>" required><br><br>
        <button type="submit">Update Bus</button>
    </form>

    <script>
        const input = document.getElementById("bus_no");
        input.addEventListener("input", function() {
            this.value = this.value.toUpperCase();
        });

        function validateBusNo() {
            const pattern = /^[A-Z]{2}-\d{2}-[A-Z]{1}-\d{4}$/;
            const value = input.value.trim();

            if (!pattern.test(value)) {
                alert("Invalid Bus Number Format. Use GJ-18-A-0001");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
