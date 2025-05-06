<!-- trip_manage.php -->

<?php
include 'db.php';
include 'navbar.php';
session_start();

// Fetch all trips from the database
$trips = $conn->query("SELECT * FROM trip_details ORDER BY date ASC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        button { padding: 6px 12px; margin: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h2>Manage Trips</h2>

    <!-- Trip Table -->
    <h3>All Trips</h3>
    <table>
        <thead>
            <tr>
                <th>Source</th>
                <th>Destination</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Payment Status</th>
                <th>Completion Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($trip = $trips->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($trip['source']) ?></td>
                    <td><?= htmlspecialchars($trip['destination']) ?></td>
                    <td><?= $trip['date'] ?></td>
                    <td><?= $trip['amount'] ?></td>
                    <td>
                        <form action="trip_manage.php" method="POST">
                            <input type="hidden" name="trip_id" value="<?= $trip['id'] ?>">
                            <select name="payment_status" onchange="this.form.submit()">
                                <option value="pending" <?= isset($trip['payment_status']) && $trip['payment_status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="completed" <?= isset($trip['payment_status']) && $trip['payment_status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <form action="trip_manage.php" method="POST">
                            <input type="hidden" name="trip_id" value="<?= $trip['id'] ?>">
                            <select name="completed" onchange="this.form.submit()">
                                <option value="0" <?= isset($trip['completed']) && $trip['completed'] == 0 ? 'selected' : '' ?>>Not Completed</option>
                                <option value="1" <?= isset($trip['completed']) && $trip['completed'] == 1 ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <!-- Edit and Delete buttons -->
                        <a href="edit_trip.php?id=<?= $trip['id'] ?>">Edit</a> | 
                        <a href="delete_trip.php?id=<?= $trip['id'] ?>" onclick="return confirm('Are you sure you want to delete this trip?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php
    // Update payment status or completion status
    if (isset($_POST['trip_id'])) {
        $trip_id = $_POST['trip_id'];

        if (isset($_POST['payment_status'])) {
            $payment_status = $_POST['payment_status'];
            $sql = "UPDATE trip_details SET payment_status = '$payment_status' WHERE id = $trip_id";
            $conn->query($sql);
        }

        if (isset($_POST['completed'])) {
            $completed = $_POST['completed'];
            $sql = "UPDATE trip_details SET completed = '$completed' WHERE id = $trip_id";
            $conn->query($sql);
        }

        echo "<script>window.location.href='trip_manage.php';</script>";
    }
    ?>
</body>
</html>
