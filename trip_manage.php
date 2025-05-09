<?php 
include 'db.php';
include 'navbar.php';
session_start();

// Fetch all trips and ingredient details
$trips = $conn->query("
    SELECT td.*, 
           b.bus_no,
           mi1.name AS breakfast_name, 
           mi2.name AS lunch_name, 
           mi3.name AS dinner_name,
           -- Fetch ingredients with their quantities and units for each meal
           (SELECT GROUP_CONCAT(CONCAT(i.name, ' ', mi.quantity, ' ', iu.unit) SEPARATOR ', ') 
            FROM meal_ingredients mi 
            JOIN ingredients i ON mi.ingredient_id = i.id
            JOIN ingredient_units iu ON i.id = iu.ingredient_id
            WHERE mi.meal_id = td.breakfast_meal_id) AS breakfast_ingredients,
           (SELECT GROUP_CONCAT(CONCAT(i.name, ' ', mi.quantity, ' ', iu.unit) SEPARATOR ', ') 
            FROM meal_ingredients mi 
            JOIN ingredients i ON mi.ingredient_id = i.id
            JOIN ingredient_units iu ON i.id = iu.ingredient_id
            WHERE mi.meal_id = td.lunch_meal_id) AS lunch_ingredients,
           (SELECT GROUP_CONCAT(CONCAT(i.name, ' ', mi.quantity, ' ', iu.unit) SEPARATOR ', ') 
            FROM meal_ingredients mi 
            JOIN ingredients i ON mi.ingredient_id = i.id
            JOIN ingredient_units iu ON i.id = iu.ingredient_id
            WHERE mi.meal_id = td.dinner_meal_id) AS dinner_ingredients
    FROM trip_details td
    LEFT JOIN buses b ON td.bus_id = b.id
    LEFT JOIN meal_items mi1 ON td.breakfast_meal_id = mi1.id
    LEFT JOIN meal_items mi2 ON td.lunch_meal_id = mi2.id
    LEFT JOIN meal_items mi3 ON td.dinner_meal_id = mi3.id
    ORDER BY td.date ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trip Management</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        h2 {
            text-align: center;
            padding: 20px 0;
            background-color: #2c3e50;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            background-color: #fff;
            transition: background-color 0.3s;
        }
        th {
            background-color: #2c3e50;
            color: white;
        }
        tr:hover td {
            background-color: #ecf0f1;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        select {
            padding: 6px;
            font-size: 14px;
            margin: 5px 0;
        }
        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .actions a {
            color: #e74c3c;
            text-decoration: none;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .export-btn {
            padding: 6px 12px;
            background-color: #16a085;
            border: none;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .export-btn:hover {
            background-color: #1abc9c;
        }
    </style>
</head>
<body>

    <h2>Manage Trips</h2>
    <table>
        <thead>
            <tr>
                <th>Source</th>
                <th>Destination</th>
                <th>Date</th>
                <th>Bus Number</th>
                <th>Number of Days</th>
                <th>Breakfast</th>
                <th>Lunch</th>
                <th>Dinner</th>
                <th>Payment Status</th>
                <th>Completed</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($trip = $trips->fetch_assoc()): ?>
            <tr class="trip-row">
                <td><?= htmlspecialchars($trip['source']) ?></td>
                <td><?= htmlspecialchars($trip['destination']) ?></td>
                <td><?= $trip['date'] ?></td>
                <td><?= $trip['bus_no'] ?: 'N/A' ?></td>
                <td><?= $trip['days'] ?: 'N/A' ?></td> <!-- Number of Days -->
                <td><?= $trip['breakfast_name'] ?: 'N/A' ?></td>
                <td><?= $trip['lunch_name'] ?: 'N/A' ?></td>
                <td><?= $trip['dinner_name'] ?: 'N/A' ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="trip_id" value="<?= $trip['id'] ?>">
                        <select name="payment_status" onchange="this.form.submit()">
                            <option value="pending" <?= $trip['payment_status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="completed" <?= $trip['payment_status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </form>
                </td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="trip_id" value="<?= $trip['id'] ?>">
                        <select name="completed" onchange="this.form.submit()">
                            <option value="0" <?= $trip['completed'] == 0 ? 'selected' : '' ?>>Not Completed</option>
                            <option value="1" <?= $trip['completed'] == 1 ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </form>
                </td>
                <td class="actions">
                    <a href="edit_trip.php?id=<?= $trip['id'] ?>">Edit</a> |
                    <a href="delete_trip.php?id=<?= $trip['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a><br>
                    <button class="export-btn" onclick='downloadTripPDF(<?= json_encode($trip, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_APOS) ?>)'>Export PDF</button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <?php
    // Handle status update
    if (isset($_POST['trip_id'])) {
        $trip_id = $_POST['trip_id'];
        if (isset($_POST['payment_status'])) {
            $conn->query("UPDATE trip_details SET payment_status = '{$_POST['payment_status']}' WHERE id = $trip_id");
        }
        if (isset($_POST['completed'])) {
            $conn->query("UPDATE trip_details SET completed = '{$_POST['completed']}' WHERE id = $trip_id");
        }
        echo "<script>window.location.href='trip_manage.php';</script>";
    }
    ?>

    <!-- jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        function downloadTripPDF(trip) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Page 1 – Trip Details
            doc.setFont("helvetica", "bold");
            doc.setFontSize(16);
            doc.text("Trip Details", 20, 20);

            doc.setFont("helvetica", "normal");
            doc.setFontSize(12);
            let y = 30;

            const info = [
                `Trip ID: ${trip.id}`,
                `Source: ${trip.source}`,
                `Destination: ${trip.destination}`,
                `Date: ${trip.date}`,
                `Bus Number: ${trip.bus_no || 'N/A'}`,
                `Amount: ₹${trip.amount}`,
                `Payment Status: ${trip.payment_status}`,
                `Completed: ${trip.completed == 1 ? 'Yes' : 'No'}`,
                `Number of Days: ${trip.days}` // Showing number of days here
            ];

            info.forEach(line => {
                doc.text(line, 20, y);
                y += 10;
            });

            // Page 2 – Day Wise Meals
            doc.addPage();
            doc.setFontSize(14);
            doc.setFont("helvetica", "bold");
            doc.text("Day Wise Meal Details", 20, 20);

            doc.setFont("helvetica", "normal");
            doc.setFontSize(12);
            let y2 = 35;

            // Get the number of days (from the 'days' field in your database)
            const dayCount = trip.days; // Assuming you have a 'days' field in the trip details

            // Dynamic loop for days of the trip
            for (let i = 1; i <= dayCount; i++) {
                doc.text(`Day ${i}`, 20, y2);
                y2 += 8;

                // Define meal section function
                const mealSection = (mealName, ingredients) => {
                    doc.text(`${mealName}:`, 20, y2);
                    y2 += 8;
                    const ingList = ingredients ? ingredients.split(",") : ["N/A"];
                    ingList.forEach((item, idx) => {
                        doc.text(`• ${item.trim()}`, 25, y2);
                        y2 += 7;
                        if (y2 > 270) {
                            doc.addPage();
                            y2 = 20;
                        }
                    });
                    y2 += 10;
                };

                // Show meals for each day
                mealSection("Breakfast Ingredients", trip.breakfast_ingredients);
                mealSection("Lunch Ingredients", trip.lunch_ingredients);
                mealSection("Dinner Ingredients", trip.dinner_ingredients);
            }

            // Save the PDF
            doc.save(`trip_${trip.id}.pdf`);
        }
    </script>
</body>
</html>
