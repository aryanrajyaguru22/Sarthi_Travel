<?php
include 'db.php';
session_start();

$all_units = ['Kg', 'Gram', 'Litre', 'ML', 'Piece', 'Packet'];
$id = intval($_GET['id']);

$ingredient = $conn->query("SELECT * FROM ingredients WHERE id=$id")->fetch_assoc();
if (!$ingredient) {
    echo "<script>alert('Not found'); window.location='ingredients.php';</script>";
    exit;
}

$existing_units = [];
$res = $conn->query("SELECT unit FROM ingredient_units WHERE ingredient_id=$id");
while ($r = $res->fetch_assoc()) $existing_units[] = $r['unit'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $units = $_POST['units'] ?? [];

    if (empty($name) || count($units) === 0) {
        echo "<script>alert('Please provide name and at least one unit');</script>";
    } else {
        $conn->query("UPDATE ingredients SET name='$name' WHERE id=$id");
        $conn->query("DELETE FROM ingredient_units WHERE ingredient_id=$id");

        foreach ($units as $unit) {
            if (in_array($unit, $all_units)) {
                $conn->query("INSERT INTO ingredient_units (ingredient_id, unit) VALUES ($id, '$unit')");
            }
        }
        echo "<script>alert('Updated'); window.location='ingredients.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ingredient</title>
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
            max-width: 600px;
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
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus {
            border-color: #4CAF50;
        }
        .unit-checkbox {
            margin: 5px 0;
        }
        button {
            width: 100%;
            padding: 12px;
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

<h2>Edit Ingredient</h2>
<div class="form-container">
    <form method="POST" onsubmit="return validateForm()">
        <label for="name">Ingredient Name:</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($ingredient['name']) ?>" required>

        <label>Select Units:</label><br>
        <?php foreach ($all_units as $unit): ?>
            <div class="unit-checkbox">
                <input type="checkbox" name="units[]" value="<?= $unit ?>" <?= in_array($unit, $existing_units) ? 'checked' : '' ?>> <?= $unit ?>
            </div>
        <?php endforeach; ?>

        <button type="submit">Update Ingredient</button>
        <p id="error-message" class="alert"></p>
    </form>
</div>

<script>
    const nameInput = document.getElementById("name");
    const errorMessage = document.getElementById("error-message");

    // Function to validate form before submission
    function validateForm() {
        const name = nameInput.value.trim();
        const units = document.querySelectorAll('input[name="units[]"]:checked');
        const unitCount = units.length;

        if (name === "" || unitCount === 0) {
            errorMessage.textContent = "Please provide name and at least one unit.";
            return false;
        }

        errorMessage.textContent = ""; // Clear any previous error message
        return true;
    }

    // Animation on input field focus
    nameInput.addEventListener("focus", function() {
        this.style.borderColor = "#4CAF50";
    });

    nameInput.addEventListener("blur", function() {
        if (this.value === "") {
            this.style.borderColor = "#ccc";
        }
    });
</script>

</body>
</html>
