<!-- meal_items.php -->


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
        body { font-family: Arial; margin: 20px; }
        input, select { margin-bottom: 10px; padding: 5px; }
        button { padding: 6px 12px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; }
        th { background-color: #f2f2f2; }
    </style>
    <script>
        function addIngredientRow() {
            const template = document.getElementById('ingredient-template');
            const clone = template.cloneNode(true);
            clone.style.display = 'block';
            document.getElementById('ingredients-list').appendChild(clone);
        }
    </script>
</head>
<body>
    <h2>Add Meal Item</h2>
    <form method="POST">
        <input type="text" name="meal_name" placeholder="Meal Name" required><br>

        <div id="ingredients-list">
            <div id="ingredient-template" style="display: none;">
                <select name="ingredient_id[]">
                    <option value="">-- Select Ingredient --</option>
                    <?php $all_ingredients->data_seek(0); while ($ing = $all_ingredients->fetch_assoc()): ?>
                        <option value="<?= $ing['id'] ?>"><?= htmlspecialchars($ing['name']) ?></option>
                    <?php endwhile; ?>
                </select>
                Quantity: <input type="text" name="quantity[]" placeholder="e.g., 100g or 1 Kg"><br><br>
            </div>
        </div>
        <button type="button" onclick="addIngredientRow()">+ Add Ingredient</button><br><br>
        <button type="submit" name="add_meal">Add Meal</button>
    </form>

    <hr>

    <h3>All Meal Items</h3>
    <table>
        <tr><th>ID</th><th>Name</th><th>Ingredients</th><th>Actions</th></tr>
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
                    <a href="?delete=<?= $meal['id'] ?>" onclick="return confirm('Delete this meal?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
