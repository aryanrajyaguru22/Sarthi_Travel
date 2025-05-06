<!-- edit_meal.php -->

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
<html>
<head>
    <title>Edit Meal</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        input, select { margin-bottom: 10px; padding: 5px; }
        button { padding: 6px 12px; }
    </style>
    <script>
        function addIngredientRow() {
            const row = document.getElementById('ingredient-template').cloneNode(true);
            row.style.display = 'block';
            document.getElementById('ingredients-list').appendChild(row);
        }
    </script>
</head>
<body>
    <h2>Edit Meal Item</h2>
    <form method="POST">
        <label>Meal Name:</label><br>
        <input type="text" name="meal_name" value="<?= htmlspecialchars($meal['name']) ?>" required><br><br>

        <div id="ingredients-list">
            <?php foreach ($ingredient_map as $ing_id => $qty): ?>
                <div>
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
                    Quantity: <input type="text" name="quantity[]" value="<?= htmlspecialchars($qty) ?>">
                    <br><br>
                </div>
            <?php endforeach; ?>
            <div id="ingredient-template" style="display:none;">
                <select name="ingredient_id[]">
                    <option value="">-- Select Ingredient --</option>
                    <?php
                    $all_ingredients->data_seek(0);
                    while ($ing = $all_ingredients->fetch_assoc()):
                    ?>
                        <option value="<?= $ing['id'] ?>"><?= htmlspecialchars($ing['name']) ?></option>
                    <?php endwhile; ?>
                </select>
                Quantity: <input type="text" name="quantity[]" placeholder="e.g. 2 Kg"><br><br>
            </div>
        </div>
        <button type="button" onclick="addIngredientRow()">+ Add Ingredient</button><br><br>
        <button type="submit">Update Meal</button>
        <a href="meal_items.php"><button type="button">Cancel</button></a>
    </form>
</body>
</html>
