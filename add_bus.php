<!-- add_bus.php -->

<?php
include 'db.php';
include 'navbar.php';
session_start();

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_no = strtoupper(trim($_POST['bus_no']));
    $pattern = "/^[A-Z]{2}-\d{2}-[A-Z]{1}-\d{4}$/";

    // Server-side validation
    if (!preg_match($pattern, $bus_no)) {
        echo "<script>alert('Invalid Bus Number Format. Use GJ-18-A-0001');</script>";
    } else {
        $check = $conn->query("SELECT * FROM buses WHERE bus_no = '$bus_no'");
        if ($check->num_rows > 0) {
            echo "<script>alert('Bus number already exists.');</script>";
        } else {
            $insert = $conn->query("INSERT INTO buses (bus_no) VALUES ('$bus_no')");
            if ($insert) {
                echo "<script>alert('Bus added successfully.'); window.location='add_bus.php';</script>";
            } else {
                echo "<script>alert('Failed to add bus.');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Bus - Sarthi Travels</title>
</head>
<body>
    <h2>Add New Bus</h2>
    <form method="POST" onsubmit="return validateBusNo()">
        <label>Bus Number (Format: GJ-18-A-0001):</label><br>
        <input type="text" name="bus_no" id="bus_no" required><br><br>
        <button type="submit">Add Bus</button>
    </form>

    <script>
        function validateBusNo() {
            const busNo = document.getElementById("bus_no").value.trim().toUpperCase();
            const pattern = /^[A-Z]{2}-\d{2}-[A-Z]{1}-\d{4}$/;

            if (!pattern.test(busNo)) {
                alert("Invalid Bus Number Format. Use GJ-18-A-0001");
                return false;
            }
            return true;
        }
    </script>

    <br><br>
    <h3>All Buses</h3>
    <table border="1" cellpadding="10">
        <tr><th>ID</th><th>Bus No</th><th>Action</th></tr>
        <?php
        $result = $conn->query("SELECT * FROM buses ORDER BY id DESC");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['bus_no']}</td>
                    <td>
                        <a href='edit_bus.php?id={$row['id']}'>Edit</a> | 
                        <a href='delete_bus.php?id={$row['id']}' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                    </td>
                </tr>";
        }
        ?>
    </table>
</body>
</html>
