<?php
include 'db.php';
include 'navbar.php';
session_start();

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('Invalid Trip ID'); window.location.href='trip_manage.php';</script>";
    exit;
}

// Fetch the trip
$trip = $conn->query("SELECT * FROM trip_details WHERE id = $id")->fetch_assoc();
if (!$trip) {
    echo "<script>alert('Trip not found'); window.location.href='trip_manage.php';</script>";
    exit;
}

// Decode meal JSON
$meal_items_data = json_decode($trip['meal_items'], true) ?? [];

// Fetch buses and meal items
$buses = $conn->query("SELECT * FROM buses ORDER BY bus_no ASC");
$meal_items = $conn->query("SELECT * FROM meal_items ORDER BY name ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $date = $_POST['date'];
    $bus_id = $_POST['bus_id'];
    $km = $_POST['km'];
    $amount = $_POST['amount'];
    $days = $_POST['trip_day'];

    $mealData = [];
    for ($day = 1; $day <= $days; $day++) {
        $mealData["day_$day"] = [
            "breakfast" => $_POST["meal"]["breakfast"][$day] ?? null,
            "lunch" => $_POST["meal"]["lunch"][$day] ?? null,
            "dinner" => $_POST["meal"]["dinner"][$day] ?? null,
        ];
    }

    // Optional: Store day 1 meals in dedicated columns
    $breakfast_meal_id = $_POST["meal"]["breakfast"][1] ?? null;
    $lunch_meal_id = $_POST["meal"]["lunch"][1] ?? null;
    $dinner_meal_id = $_POST["meal"]["dinner"][1] ?? null;

    $meal_json = json_encode($mealData);

    $stmt = $conn->prepare("UPDATE trip_details SET 
        source = ?, destination = ?, date = ?, bus_id = ?, km = ?, meal_items = ?, 
        breakfast_meal_id = ?, lunch_meal_id = ?, dinner_meal_id = ?, amount = ?, days = ?
        WHERE id = ?");

    $stmt->bind_param("sssissiiidii", $source, $destination, $date, $bus_id, $km, $meal_json,
        $breakfast_meal_id, $lunch_meal_id, $dinner_meal_id, $amount, $days, $id);

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
    <script>
        function showMealSelectors() {
            const container = document.getElementById("meal_selectors");
            container.innerHTML = "";
            const days = document.getElementById("trip_day").value;
            for (let i = 1; i <= days; i++) {
                container.innerHTML += `
                    <h4>Day ${i}</h4>
                    <label>Breakfast:</label><br>
                    <select name="meal[breakfast][${i}]">${document.getElementById("meal_options").innerHTML}</select><br>
                    <label>Lunch:</label><br>
                    <select name="meal[lunch][${i}]">${document.getElementById("meal_options").innerHTML}</select><br>
                    <label>Dinner:</label><br>
                    <select name="meal[dinner][${i}]">${document.getElementById("meal_options").innerHTML}</select><br><br>
                `;
            }

            setTimeout(() => {
                const meals = <?= json_encode($meal_items_data) ?>;
                for (let day in meals) {
                    const dayNum = parseInt(day.replace("day_", ""));
                    if (meals[day]) {
                        document.querySelector(`[name="meal[breakfast][${dayNum}]"]`).value = meals[day].breakfast;
                        document.querySelector(`[name="meal[lunch][${dayNum}]"]`).value = meals[day].lunch;
                        document.querySelector(`[name="meal[dinner][${dayNum}]"]`).value = meals[day].dinner;
                    }
                }
            }, 100);
        }
    </script>
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
            <option value="">Select Bus</option>
            <?php while ($bus = $buses->fetch_assoc()): ?>
                <option value="<?= $bus['id'] ?>" <?= $trip['bus_id'] == $bus['id'] ? 'selected' : '' ?>>
                    <?= $bus['bus_no'] ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Distance (Km):</label><br>
        <input type="number" name="km" value="<?= $trip['km'] ?>" required><br><br>

        <label>Amount (INR):</label><br>
        <input type="number" name="amount" step="0.01" value="<?= $trip['amount'] ?>" required><br><br>

        <label>Number of Days:</label><br>
        <input type="number" name="trip_day" id="trip_day" value="<?= $trip['days'] ?>" required oninput="showMealSelectors()"><br><br>

        <!-- Hidden meal options for JS -->
        <div id="meal_options" style="display:none;">
            <option value="">Select Meal</option>
            <?php
            $meal_items->data_seek(0);
            while ($meal = $meal_items->fetch_assoc()):
            ?>
                <option value="<?= $meal['id'] ?>"><?= htmlspecialchars($meal['name']) ?></option>
            <?php endwhile; ?>
        </div>

        <div id="meal_selectors"></div>

        <button type="submit">Update Trip</button>
    </form>

    <script> window.onload = showMealSelectors; </script>
</body>
</html>
