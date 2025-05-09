<?php
include 'db.php';
include 'navbar.php';
session_start();

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('Invalid Trip ID'); window.location.href='trip_manage.php';</script>";
    exit;
}

// Get the first record to fetch main trip info
$trip = $conn->query("SELECT * FROM trip_details WHERE id = $id")->fetch_assoc();
$trip_group_id = $trip['group_id'] ?? $trip['id']; // assume group_id or fallback to id
$trip_days = $conn->query("SELECT * FROM trip_details WHERE group_id = $trip_group_id ORDER BY id ASC");

// Buses and meals
$buses = $conn->query("SELECT * FROM buses ORDER BY bus_no ASC");
$meal_items = $conn->query("SELECT * FROM meal_items ORDER BY name ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $date = $_POST['date'];
    $bus_id = $_POST['bus_id'];
    $km = $_POST['km'];
    $amount = $_POST['amount'];
    $trip_day = $_POST['trip_day'];

    // First delete existing trip day entries for that group
    $conn->query("DELETE FROM trip_details WHERE group_id = $trip_group_id");

    $stmt = $conn->prepare("INSERT INTO trip_details (source, destination, date, bus_id, km, amount, trip_day, breakfast_meal_id, lunch_meal_id, dinner_meal_id, group_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    for ($day = 1; $day <= $trip_day; $day++) {
        $breakfast = $_POST['meal']['breakfast'][$day] ?? null;
        $lunch = $_POST['meal']['lunch'][$day] ?? null;
        $dinner = $_POST['meal']['dinner'][$day] ?? null;
        $stmt->bind_param("sssiiiiiiii", $source, $destination, $date, $bus_id, $km, $amount, $trip_day, $breakfast, $lunch, $dinner, $trip_group_id);
        $stmt->execute();
    }

    echo "<script>alert('Trip updated successfully'); window.location.href='trip_manage.php';</script>";
    exit;
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
            const days = document.getElementById("trip_day").value;
            const mealOptions = document.getElementById("meal_options").innerHTML;
            container.innerHTML = "";

            for (let i = 1; i <= days; i++) {
                container.innerHTML += `
                    <h4>Day ${i}</h4>
                    <label>Breakfast:</label><br>
                    <select name="meal[breakfast][${i}]">${mealOptions}</select><br>
                    <label>Lunch:</label><br>
                    <select name="meal[lunch][${i}]">${mealOptions}</select><br>
                    <label>Dinner:</label><br>
                    <select name="meal[dinner][${i}]">${mealOptions}</select><br><br>
                `;
            }
        }

        function setDefaultMeals(tripData) {
            const days = Object.keys(tripData).length;
            document.getElementById("trip_day").value = days;
            showMealSelectors();

            for (let i = 1; i <= days; i++) {
                if (tripData[i]) {
                    document.querySelector(`[name="meal[breakfast][${i}]"]`).value = tripData[i].breakfast;
                    document.querySelector(`[name="meal[lunch][${i}]"]`).value = tripData[i].lunch;
                    document.querySelector(`[name="meal[dinner][${i}]"]`).value = tripData[i].dinner;
                }
            }
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
            <?php while ($bus = $buses->fetch_assoc()): ?>
                <option value="<?= $bus['id'] ?>" <?= $trip['bus_id'] == $bus['id'] ? 'selected' : '' ?>>
                    <?= $bus['bus_no'] ?> (<?= $bus['bus_type'] ?>)
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Trip Distance (Km):</label><br>
        <input type="number" name="km" value="<?= $trip['km'] ?>" required><br><br>

        <label>Amount (INR):</label><br>
        <input type="number" name="amount" value="<?= $trip['amount'] ?>" required><br><br>

        <label>Number of Days:</label><br>
        <input type="number" name="trip_day" id="trip_day" value="<?= $trip['trip_day'] ?>" required oninput="showMealSelectors()"><br><br>

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

    <script>
        const tripData = {
            <?php
            $i = 1;
            while ($day = $trip_days->fetch_assoc()):
                echo "$i: { breakfast: '{$day['breakfast_meal_id']}', lunch: '{$day['lunch_meal_id']}', dinner: '{$day['dinner_meal_id']}' },";
                $i++;
            endwhile;
            ?>
        };
        window.onload = () => setDefaultMeals(tripData);
    </script>
</body>
</html>
