<!-- edit_ingredient.php -->

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
<html>
<head>
    <title>Edit Ingredient</title>
</head>
<body>
    <h2>Edit Ingredient</h2>
    <form method="POST">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($ingredient['name']) ?>" required><br><br>

        <label>Select Units:</label><br>
        <?php foreach ($all_units as $unit): ?>
            <input type="checkbox" name="units[]" value="<?= $unit ?>" <?= in_array($unit, $existing_units) ? 'checked' : '' ?>> <?= $unit ?><br>
        <?php endforeach; ?>
        <br>
        <button type="submit">Update</button>
    </form>
</body>
</html>
