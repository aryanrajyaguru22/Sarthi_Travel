<!-- trip.php -->
<?php
include 'db.php';
include 'navbar.php';
session_start();

// Fetch all buses, meal items, and ingredients
$buses = $conn->query("SELECT * FROM buses ORDER BY bus_no ASC");
$meal_items = $conn->query("SELECT * FROM meal_items ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Trip</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        select, input { margin-bottom: 10px; padding: 5px; width: 300px; }
        button { padding: 6px 12px; }
    </style>
</head>
<body>
    <h2>Create Trip</h2>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $source = $_POST['source'];
        $destination = $_POST['destination'];
        $date = $_POST['date'];
        $bus_id = $_POST['bus_id'];
        $km = $_POST['km'];
        $breakfast = $_POST['meal']['breakfast'];
        $lunch = $_POST['meal']['lunch'];
        $dinner = $_POST['meal']['dinner'];

        $stmt = $conn->prepare("INSERT INTO trip_details (source, destination, date, bus_id, km, breakfast_meal_id, lunch_meal_id, dinner_meal_id)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiiiii", $source, $destination, $date, $bus_id, $km, $breakfast, $lunch, $dinner);

        if ($stmt->execute()) {
            echo "<script>alert('Trip created successfully'); window.location.href='trip_manage.php';</script>";
        } else {
            echo "<script>alert('Error creating trip');</script>";
        }
        $stmt->close();
    }
    ?>

    <form method="POST" action="">
        <label>Source:</label><br>
        <input type="text" name="source" required><br><br>

        <label>Destination:</label><br>
        <input type="text" name="destination" required><br><br>

        <label>Date:</label><br>
        <input type="date" name="date" required><br><br>

        <label>Bus:</label><br>
        <select name="bus_id" required>
            <option value="">Select Bus</option>
            <?php while ($bus = $buses->fetch_assoc()): ?>
                <option value="<?= $bus['id'] ?>"><?= $bus['bus_no'] ?> </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Trip Distance (Km):</label><br>
        <input type="number" name="km" required><br><br>

        <label>Breakfast:</label><br>
        <select name="meal[breakfast]">
            <option value="">Select Breakfast Meal</option>
            <?php while ($meal = $meal_items->fetch_assoc()): ?>
                <option value="<?= $meal['id'] ?>"><?= htmlspecialchars($meal['name']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Lunch:</label><br>
        <select name="meal[lunch]">
            <option value="">Select Lunch Meal</option>
            <?php
            $meal_items->data_seek(0); // Reset pointer
            while ($meal = $meal_items->fetch_assoc()): ?>
                <option value="<?= $meal['id'] ?>"><?= htmlspecialchars($meal['name']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Dinner:</label><br>
        <select name="meal[dinner]">
            <option value="">Select Dinner Meal</option>
            <?php
            $meal_items->data_seek(0); // Reset pointer
            while ($meal = $meal_items->fetch_assoc()): ?>
                <option value="<?= $meal['id'] ?>"><?= htmlspecialchars($meal['name']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Create Trip</button>
    </form>
</body>
</html>
