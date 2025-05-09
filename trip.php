<!-- trip.php -->
<?php
include 'db.php';
include 'navbar.php';
session_start();

// Fetch all buses and meal items
$buses = $conn->query("SELECT * FROM buses ORDER BY bus_no ASC");
$meal_items = $conn->query("SELECT * FROM meal_items ORDER BY name ASC");

// Handle POST submission
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

    $stmt = $conn->prepare("INSERT INTO trip_details 
        (source, destination, date, bus_id, km, meal_items, breakfast_meal_id, lunch_meal_id, dinner_meal_id, amount, days)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssissiiiid", $source, $destination, $date, $bus_id, $km, $meal_json, $breakfast_meal_id, $lunch_meal_id, $dinner_meal_id, $amount, $days);

    if ($stmt->execute()) {
        echo "<script>alert('Trip created successfully'); window.location.href='trip_manage.php';</script>";
    } else {
        echo "<script>alert('Error creating trip');</script>";
    }

    $stmt->close();
}
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
        }
    </script>
</head>
<body>
    <h2>Create Trip</h2>

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
                <option value="<?= $bus['id'] ?>"><?= $bus['bus_no'] ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Distance (Km):</label><br>
        <input type="number" name="km" required><br><br>

        <label>Amount (INR):</label><br>
        <input type="number" name="amount" step="0.01" required><br><br>

        <label>Number of Days:</label><br>
        <input type="number" name="trip_day" id="trip_day" required oninput="showMealSelectors()"><br><br>

        <!-- Hidden meal options for JS to clone -->
        <div id="meal_options" style="display:none;">
            <option value="">Select Meal</option>
            <?php
            $meal_items->data_seek(0);
            while ($meal = $meal_items->fetch_assoc()): ?>
                <option value="<?= $meal['id'] ?>"><?= htmlspecialchars($meal['name']) ?></option>
            <?php endwhile; ?>
        </div>

        <div id="meal_selectors"></div>

        <button type="submit">Create Trip</button>
    </form>
</body>
</html>
