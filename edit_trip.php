<!-- edit_trip.php -->

<?php
include 'db.php';
include 'navbar.php';
session_start();

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('Invalid Trip ID'); window.location.href='trip_manage.php';</script>";
    exit;
}

$result = $conn->query("SELECT * FROM trip_details WHERE id = $id");
$trip = $result->fetch_assoc();

$buses = $conn->query("SELECT * FROM buses ORDER BY bus_no ASC");
$meal_items = $conn->query("SELECT * FROM meal_items ORDER BY name ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $date = $_POST['date'];
    $bus_id = $_POST['bus_id'];
    $km = $_POST['km'];
    $breakfast = $_POST['meal']['breakfast'];
    $lunch = $_POST['meal']['lunch'];
    $dinner = $_POST['meal']['dinner'];

    $stmt = $conn->prepare("UPDATE trip_details SET source=?, destination=?, date=?, bus_id=?, km=?, breakfast_meal_id=?, lunch_meal_id=?, dinner_meal_id=? WHERE id=?");
    $stmt->bind_param("sssiiiiii", $source, $destination, $date, $bus_id, $km, $breakfast, $lunch, $dinner, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Trip updated successfully'); window.location.href='trip_manage.php';</script>";
    } else {
        echo "<script>alert('Error updating trip');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Trip</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        select, input { margin-bottom: 10px; padding: 5px; width: 300px; }
        button { padding: 6px 12px; }
    </style>
</head>
<body>
    <h2>Edit Trip</h2>
    <form method="POST">
        <label>Source:</label><br>
        <input type="text" name="source" value="<?= $trip['source'] ?>" required><br><br>

        <label>Destination:</label><br>
        <input type="text" name="destination" value="<?= $trip['destination'] ?>" required><br><br>

        <label>Date:</label><br>
        <input type="date" name="date" value="<?= $trip['date'] ?>" required><br><br>

        <label>Bus:</label><br>
        <select name="bus_id" required>
            <?php while ($bus = $buses->fetch_assoc()): ?>
                <option value="<?= $bus['id'] ?>" <?= $trip['bus_id'] == $bus['id'] ? 'selected' : '' ?>>
                    <?= $bus['bus_no'] ?> (<?= $bus['bus_type'] ?>)
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Trip Distance (Km):</label><br>
        <input type="number" name="km" value="<?= $trip['km'] ?>" required><br><br>

        <label>Breakfast:</label><br>
        <select name="meal[breakfast]">
            <option value="">Select Breakfast Meal</option>
            <?php
            $meal_items->data_seek(0);
            while ($meal = $meal_items->fetch_assoc()):
            ?>
                <option value="<?= $meal['id'] ?>" <?= $trip['breakfast_meal_id'] == $meal['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($meal['name']) ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Lunch:</label><br>
        <select name="meal[lunch]">
            <option value="">Select Lunch Meal</option>
            <?php
            $meal_items->data_seek(0);
            while ($meal = $meal_items->fetch_assoc()):
            ?>
                <option value="<?= $meal['id'] ?>" <?= $trip['lunch_meal_id'] == $meal['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($meal['name']) ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Dinner:</label><br>
        <select name="meal[dinner]">
            <option value="">Select Dinner Meal</option>
            <?php
            $meal_items->data_seek(0);
            while ($meal = $meal_items->fetch_assoc()):
            ?>
                <option value="<?= $meal['id'] ?>" <?= $trip['dinner_meal_id'] == $meal['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($meal['name']) ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Update Trip</button>
    </form>
</body>
</html>
