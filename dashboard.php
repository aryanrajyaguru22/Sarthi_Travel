<?php
include 'db.php';
include 'navbar.php';
session_start();

// Fetch all trips for calendar (excluding completed)
$trips_result = $conn->query("SELECT * FROM trip_details WHERE completed = 0 ORDER BY date ASC");

$calendar_events = [];
while ($trip = $trips_result->fetch_assoc()) {
    $calendar_events[] = [
        'title' => $trip['source'] . ' to ' . $trip['destination'],
        'start' => $trip['date'],
        'description' => 'Amount: ₹' . $trip['amount'],
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        #calendar { max-width: 900px; margin: 0 auto; }
        .trip-details { margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
    </style>
</head>
<body>

<h2>Dashboard</h2>

<!-- Calendar Section -->
<div id="calendar"></div>

<!-- Upcoming Trip Details Section -->
<div class="trip-details">
    <h3>Upcoming Trips (Next 7 Days)</h3>
    <table>
        <thead>
            <tr>
                <th>Trip</th>
                <th>Date</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $today = date('Y-m-d');
            $next_7_days = date('Y-m-d', strtotime('+7 days'));

            $upcoming = $conn->query("SELECT * FROM trip_details WHERE date >= '$today' AND date <= '$next_7_days' AND completed = 0 ORDER BY date ASC");

            if ($upcoming && $upcoming->num_rows > 0):
                while ($trip = $upcoming->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($trip['source']) ?> to <?= htmlspecialchars($trip['destination']) ?></td>
                    <td><?= $trip['date'] ?></td>
                    <td>₹<?= $trip['amount'] ?></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="3">No upcoming trips found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- FullCalendar Script -->
<script>
    $(document).ready(function () {
        $('#calendar').fullCalendar({
            events: <?= json_encode($calendar_events); ?>,
            eventRender: function (event, element) {
                element.attr('title', event.description);
            },
            eventClick: function (event) {
                alert('Trip: ' + event.title + '\nDate: ' + event.start.format('YYYY-MM-DD') + '\n' + event.description);
            }
        });
    });
</script>

</body>
</html>
