<?php
include 'db.php';
include 'navbar.php';
session_start();

// Handle Add Meal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_meal'])) {
    $meal_name = trim($_POST['meal_name']);
    $ingredients = $_POST['ingredient_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    if ($meal_name && count($ingredients) > 0) {
        $conn->query("INSERT INTO meal_items (name) VALUES ('$meal_name')");
        $meal_id = $conn->insert_id;

        for ($i = 0; $i < count($ingredients); $i++) {
            $ing_id = intval($ingredients[$i]);
            $qty = trim($quantities[$i]);
            if ($qty !== '') {
                $conn->query("INSERT INTO meal_ingredients (meal_id, ingredient_id, quantity) VALUES ($meal_id, $ing_id, '$qty')");
            }
        }
        echo "<script>alert('Meal item added.'); window.location='meal_items.php';</script>";
    } else {
        echo "<script>alert('Please enter meal name and ingredients.');</script>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $meal_id = intval($_GET['delete']);
    $conn->query("DELETE FROM meal_items WHERE id=$meal_id");
    echo "<script>alert('Meal deleted'); window.location='meal_items.php';</script>";
}

// Fetch all ingredients
$all_ingredients = $conn->query("SELECT * FROM ingredients ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Meal Items</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h2, h3 {
            text-align: center;
            color: #4CAF50;
        }

        .form-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        input[type="text"]:focus, select:focus {
            border-color: #4CAF50;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            color: #45a049;
        }

        .ingredient-row {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .ingredient-row input,
        .ingredient-row select {
            flex: 1;
        }

        .ingredient-row button {
            width: auto;
            background-color: #e74c3c;
            color: white;
        }

        .ingredient-row button:hover {
            background-color: #c0392b;
        }

        .confirmation-popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            max-width: 300px;
            width: 100%;
        }

        .popup-content button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            margin: 10px;
            cursor: pointer;
        }

        .popup-content button.cancel {
            background-color: #e74c3c;
        }
    </style>
    <script>
        function addIngredientRow() {
            const template = document.getElementById('ingredient-template');
            const clone = template.cloneNode(true);
            clone.style.display = 'flex';
            document.getElementById('ingredients-list').appendChild(clone);
        }

        function showConfirmationPopup(id) {
            const popup = document.getElementById("confirmation-popup");
            const deleteBtn = document.getElementById("delete-btn");
            const cancelBtn = document.getElementById("cancel-btn");

            popup.style.display = "flex";
            
            deleteBtn.onclick = function() {
                window.location.href = '?delete=' + id;
            }

            cancelBtn.onclick = function() {
                popup.style.display = "none";
            }
        }
    </script>
</head>
<body>

<div class="form-container">
    <h2>Add Meal Item</h2>
    <form method="POST">
        <input type="text" name="meal_name" placeholder="Meal Name" required>

        <div id="ingredients-list">
            <div id="ingredient-template" style="display: none;" class="ingredient-row">
                <select name="ingredient_id[]">
                    <option value="">-- Select Ingredient --</option>
                    <?php $all_ingredients->data_seek(0); while ($ing = $all_ingredients->fetch_assoc()): ?>
                        <option value="<?= $ing['id'] ?>"><?= htmlspecialchars($ing['name']) ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="text" name="quantity[]" placeholder="e.g., 100g or 1 Kg">
                <button type="button" onclick="this.parentElement.remove()">Remove</button>
            </div>
        </div>

        <button type="button" onclick="addIngredientRow()">+ Add Ingredient</button><br><br>
        <button type="submit" name="add_meal">Add Meal</button>
    </form>
</div>

<hr>

<h3>All Meal Items</h3>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Ingredients</th>
        <th>Actions</th>
    </tr>
    <?php
    $meals = $conn->query("SELECT * FROM meal_items ORDER BY id DESC");
    while ($meal = $meals->fetch_assoc()):
        $mid = $meal['id'];
        $ings = $conn->query("SELECT i.name, mi.quantity FROM meal_ingredients mi 
                              JOIN ingredients i ON mi.ingredient_id = i.id WHERE meal_id=$mid");
        $details = [];
        while ($ing = $ings->fetch_assoc()) {
            $details[] = $ing['name'] . " (" . $ing['quantity'] . ")";
        }
    ?>
        <tr>
            <td><?= $meal['id'] ?></td>
            <td><?= htmlspecialchars($meal['name']) ?></td>
            <td><?= implode(', ', $details) ?></td>
            <td>
                <a href="edit_meal.php?id=<?= $meal['id'] ?>">Edit</a> |
                <span class="delete-btn" onclick="showConfirmationPopup(<?= $meal['id'] ?>)">Delete</span>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<div id="confirmation-popup" class="confirmation-popup">
    <div class="popup-content">
        <h4>Are you sure you want to delete this meal?</h4>
        <button id="delete-btn">Yes, Delete</button>
        <button id="cancel-btn" class="cancel">Cancel</button>
    </div>
</div>

</body>
</html>
