<?php
include 'db.php';
session_start();

// Get meal ID from URL
$id = intval($_GET['id']);

// Fetch meal info
$meal = $conn->query("SELECT * FROM meal_items WHERE id=$id")->fetch_assoc();
if (!$meal) {
    echo "<script>alert('Meal not found'); window.location='meal_items.php';</script>";
    exit;
}

// Fetch ingredients list
$all_ingredients = $conn->query("SELECT * FROM ingredients ORDER BY name ASC");

// Fetch meal ingredients
$existing_ingredients = $conn->query("SELECT * FROM meal_ingredients WHERE meal_id=$id");

$ingredient_map = [];
while ($row = $existing_ingredients->fetch_assoc()) {
    $ingredient_map[$row['ingredient_id']] = $row['quantity'];
}

// Update meal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['meal_name']);
    $ingredients = $_POST['ingredient_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    if ($name && count($ingredients) > 0) {
        $conn->query("UPDATE meal_items SET name='$name' WHERE id=$id");
        $conn->query("DELETE FROM meal_ingredients WHERE meal_id=$id");

        for ($i = 0; $i < count($ingredients); $i++) {
            $ing_id = intval($ingredients[$i]);
            $qty = trim($quantities[$i]);
            if ($qty !== '') {
                $conn->query("INSERT INTO meal_ingredients (meal_id, ingredient_id, quantity) VALUES ($id, $ing_id, '$qty')");
            }
        }
        echo "<script>alert('Meal updated.'); window.location='meal_items.php';</script>";
    } else {
        echo "<script>alert('Please enter name and at least one ingredient.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Meal</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
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
            max-width: 800px;
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
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus, select:focus {
            border-color: #4CAF50;
        }
        .ingredient-row {
            margin-bottom: 15px;
        }
        .add-ingredient-btn, .submit-btn, .cancel-btn {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        .add-ingredient-btn:hover, .submit-btn:hover, .cancel-btn:hover {
            background-color: #45a049;
        }
        .ingredient-template {
            display: none;
        }
        .ingredient-row input {
            width: 30%;
            display: inline-block;
        }
        .ingredient-row select {
            width: 60%;
            display: inline-block;
        }
    </style>
    <script>
        // Function to add ingredient row dynamically
        function addIngredientRow() {
            const template = document.getElementById('ingredient-template').cloneNode(true);
            template.style.display = 'block';
            document.getElementById('ingredients-list').appendChild(template);
        }
    </script>
</head>
<body>

<div class="form-container">
    <h2>Edit Meal Item</h2>
    <form method="POST">
        <label for="meal_name">Meal Name:</label>
        <input type="text" name="meal_name" id="meal_name" value="<?= htmlspecialchars($meal['name']) ?>" required>

        <div id="ingredients-list">
            <?php foreach ($ingredient_map as $ing_id => $qty): ?>
                <div class="ingredient-row">
                    <select name="ingredient_id[]">
                        <option value="">-- Select Ingredient --</option>
                        <?php
                        $all_ingredients->data_seek(0); // reset pointer
                        while ($ing = $all_ingredients->fetch_assoc()):
                        ?>
                            <option value="<?= $ing['id'] ?>" <?= $ing['id'] == $ing_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ing['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <input type="text" name="quantity[]" value="<?= htmlspecialchars($qty) ?>" placeholder="Quantity (e.g., 2 Kg)">
                </div>
            <?php endforeach; ?>
        </div>

        <div id="ingredient-template" class="ingredient-row ingredient-template">
            <select name="ingredient_id[]">
                <option value="">-- Select Ingredient --</option>
                <?php
                $all_ingredients->data_seek(0); // reset pointer
                while ($ing = $all_ingredients->fetch_assoc()):
                ?>
                    <option value="<?= $ing['id'] ?>"><?= htmlspecialchars($ing['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <input type="text" name="quantity[]" placeholder="e.g., 2 Kg">
        </div>

        <button type="button" class="add-ingredient-btn" onclick="addIngredientRow()">+ Add Ingredient</button><br><br>
        <button type="submit" class="submit-btn">Update Meal</button>
        <a href="meal_items.php"><button type="button" class="cancel-btn">Cancel</button></a>
    </form>
</div>

</body>
</html>
