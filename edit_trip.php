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
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            color: #4CAF50;
            margin-top: 20px;
        }
        .form-container {
            width: 100%;
            max-width: 900px;
            margin: 30px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
            color: #333;
        }
        input[type="text"], input[type="date"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus, input[type="number"]:focus, input[type="date"]:focus, select:focus {
            border-color: #4CAF50;
        }
        #meal_selectors {
            margin-top: 20px;
        }
        .meal-row {
            margin-bottom: 15px;
        }
        .meal-row label {
            font-size: 14px;
            color: #555;
        }
        .meal-row select {
            width: 100%;
            padding: 8px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        button {
            padding: 12px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
        .meal-selector {
            display: none;
        }
    </style>
    <script>
        function showMealSelectors() {
            const container = document.getElementById("meal_selectors");
            container.innerHTML = "";
            const days = document.getElementById("trip_day").value;
            for (let i = 1; i <= days; i++) {
                container.innerHTML += `
                    <div class="meal-row">
                        <h4>Day ${i}</h4>
                        <label>Breakfast:</label>
                        <select name="meal[breakfast][${i}]">${document.getElementById("meal_options").innerHTML}</select><br>
                        <label>Lunch:</label>
                        <select name="meal[lunch][${i}]">${document.getElementById("meal_options").innerHTML}</select><br>
                        <label>Dinner:</label>
                        <select name="meal[dinner][${i}]">${document.getElementById("meal_options").innerHTML}</select>
                    </div>
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

<div class="form-container">
    <h2>Edit Trip</h2>

    <form method="POST">
        <label>Source:</label>
        <input type="text" name="source" value="<?= $trip['source'] ?>" required>

        <label>Destination:</label>
        <input type="text" name="destination" value="<?= $trip['destination'] ?>" required>

        <label>Date:</label>
        <input type="date" name="date" value="<?= $trip['date'] ?>" required>

        <label>Bus:</label>
        <select name="bus_id" required>
            <option value="">Select Bus</option>
            <?php while ($bus = $buses->fetch_assoc()): ?>
                <option value="<?= $bus['id'] ?>" <?= $trip['bus_id'] == $bus['id'] ? 'selected' : '' ?>>
                    <?= $bus['bus_no'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Distance (Km):</label>
        <input type="number" name="km" value="<?= $trip['km'] ?>" required>

        <label>Amount (INR):</label>
        <input type="number" name="amount" value="<?= $trip['amount'] ?>" required step="0.01">

        <label>Number of Days:</label>
        <input type="number" name="trip_day" id="trip_day" value="<?= $trip['days'] ?>" required oninput="showMealSelectors()">

        <div id="meal_options" class="meal-selector">
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
</div>

<script>
    window.onload = showMealSelectors;
</script>

</body>
</html>
