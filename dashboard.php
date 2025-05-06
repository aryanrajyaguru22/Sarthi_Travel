<!-- dashboard.php-->

<?php
include 'db.php';
include 'navbar.php';
session_start();

// Fetch trip details from the database
$trips = $conn->query("SELECT * FROM trip_details ORDER BY date ASC");

// Fetch trip data for calendar (date and trip information)
$calendar_events = [];
while ($trip = $trips->fetch_assoc()) {
    $calendar_events[] = [
        'title' => $trip['source'] . ' to ' . $trip['destination'],
        'start' => $trip['date'],
        'description' => 'Amount: ' . $trip['amount'] . ', Status: ' . ($trip['completed'] ? 'Completed' : 'Not Completed'),
        'payment_status' => $trip['payment_status'],
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.css" rel="stylesheet">

    <!-- jQuery and FullCalendar JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.js"></script>

    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        #calendar { max-width: 900px; margin: 0 auto; }
        .trip-details { margin-top: 30px; }
    </style>
</head>
<body>
    <h2>Dashboard</h2>

    <!-- Calendar Section -->
    <div id="calendar"></div>

    <!-- Trip Details Section -->
    <div class="trip-details">
        <h3>Trip Details</h3>
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>Trip</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Payment Status</th>
                    <th>Completion Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($trip = $trips->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($trip['source']) ?> to <?= htmlspecialchars($trip['destination']) ?></td>
                        <td><?= $trip['date'] ?></td>
                        <td><?= $trip['amount'] ?></td>
                        <td><?= ucfirst($trip['payment_status']) ?></td>
                        <td><?= $trip['completed'] ? 'Completed' : 'Not Completed' ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                events: <?php echo json_encode($calendar_events); ?>,
                eventRender: function(event, element) {
                    element.attr('title', event.description); // Show event details as tooltip
                },
                eventClick: function(event) {
                    alert('Trip: ' + event.title + '\nDate: ' + event.start.format('YYYY-MM-DD') + '\n' + event.description);
                }
            });
        });
    </script>
</body>
</html>
