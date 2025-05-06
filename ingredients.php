<!-- ingredients.php -->

<?php
include 'db.php';
include 'navbar.php';
session_start();

$all_units = ['Kg', 'Gram', 'Litre', 'ML', 'Piece', 'Packet'];

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $units = $_POST['units'] ?? [];

    if (empty($name) || count($units) === 0) {
        echo "<script>alert('Please enter name and select at least one unit.');</script>";
    } else {
        $conn->query("INSERT INTO ingredients (name) VALUES ('$name')");
        $ingredient_id = $conn->insert_id;

        foreach ($units as $unit) {
            $unit = trim($unit);
            if (in_array($unit, $all_units)) {
                $conn->query("INSERT INTO ingredient_units (ingredient_id, unit) VALUES ($ingredient_id, '$unit')");
            }
        }
        echo "<script>alert('Ingredient added.'); window.location='ingredients.php';</script>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM ingredients WHERE id=$id");
    echo "<script>window.location='ingredients.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ingredients</title>
</head>
<body>
    <h2>Add New Ingredient</h2>
    <form method="POST">
        <label>Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Select Units:</label><br>
        <?php foreach ($all_units as $unit): ?>
            <input type="checkbox" name="units[]" value="<?= $unit ?>"> <?= $unit ?><br>
        <?php endforeach; ?>
        <br>
        <button type="submit">Add Ingredient</button>
    </form>

    <hr>

    <h3>All Ingredients</h3>
    <table border="1" cellpadding="10">
        <tr><th>ID</th><th>Name</th><th>Units</th><th>Action</th></tr>
        <?php
        $result = $conn->query("SELECT * FROM ingredients ORDER BY id DESC");
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $unit_res = $conn->query("SELECT unit FROM ingredient_units WHERE ingredient_id=$id");
            $units = [];
            while ($u = $unit_res->fetch_assoc()) $units[] = $u['unit'];

            echo "<tr>
                    <td>{$id}</td>
                    <td>{$row['name']}</td>
                    <td>" . implode(', ', $units) . "</td>
                    <td>
                        <a href='edit_ingredient.php?id={$id}'>Edit</a> |
                        <a href='?delete={$id}' onclick='return confirm(\"Delete this?\")'>Delete</a>
                    </td>
                </tr>";
        }
        ?>
    </table>
</body>
</html>
