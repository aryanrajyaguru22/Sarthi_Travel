<!-- trip.php -->
<?php
include 'db.php';
include 'navbar.php';
session_start();

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
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #2d89ef;
            text-align: center;
            margin-bottom: 25px;
        }

        form {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: 600;
            margin-top: 10px;
            display: block;
        }

        select, input {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: border-color 0.3s;
        }

        select:focus, input:focus {
            border-color: #2d89ef;
            outline: none;
        }

        button {
            background-color: #2d89ef;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #1b5dab;
            transform: scale(1.03);
        }

        #meal_selectors {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-10px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .day-box {
            background: #eef3fb;
            border-left: 4px solid #2d89ef;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
        }
    </style>

    <script>
        function showMealSelectors() {
            const container = document.getElementById("meal_selectors");
            container.innerHTML = "";
            const days = parseInt(document.getElementById("trip_day").value);
            if (isNaN(days) || days <= 0) return;

            for (let i = 1; i <= days; i++) {
                container.innerHTML += `
                    <div class="day-box">
                        <h4>Day ${i}</h4>
                        <label>Breakfast:</label>
                        <select name="meal[breakfast][${i}]">${document.getElementById("meal_options").innerHTML}</select>
                        <label>Lunch:</label>
                        <select name="meal[lunch][${i}]">${document.getElementById("meal_options").innerHTML}</select>
                        <label>Dinner:</label>
                        <select name="meal[dinner][${i}]">${document.getElementById("meal_options").innerHTML}</select>
                    </div>
                `;
            }
        }
    </script>
</head>
<body>

<h2>Create New Trip</h2>

<form method="POST" action="">
    <label>Source:</label>
    <input type="text" name="source" required>

    <label>Destination:</label>
    <input type="text" name="destination" required>

    <label>Date:</label>
    <input type="date" name="date" required>

    <label>Bus:</label>
    <select name="bus_id" required>
        <option value="">Select Bus</option>
        <?php while ($bus = $buses->fetch_assoc()): ?>
            <option value="<?= $bus['id'] ?>"><?= $bus['bus_no'] ?></option>
        <?php endwhile; ?>
    </select>

    <label>Distance (Km):</label>
    <input type="number" name="km" required>

    <label>Amount (INR):</label>
    <input type="number" name="amount" step="0.01" required>

    <label>Number of Days:</label>
    <input type="number" name="trip_day" id="trip_day" required oninput="showMealSelectors()">

    <!-- Hidden meal options -->
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
