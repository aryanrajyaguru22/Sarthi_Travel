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
    <title>ડેશબોર્ડ</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.js"></script>
    <style>
        body { font-family: 'Arial', sans-serif; margin: 20px; background-color: #f4f4f4; }
        h2, h3 { text-align: center; color: #333; }
        #calendar { max-width: 900px; margin: 20px auto; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .trip-details { margin-top: 30px; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; font-size: 14px; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #ddd; cursor: pointer; }
        td, th { transition: background-color 0.3s ease, color 0.3s ease; }
        td:hover { background-color: #f1f1f1; }
        .no-trips { text-align: center; color: #999; }
    </style>
</head>
<body>

<h2>ડેશબોર્ડ</h2>

<!-- Calendar Section -->
<div id="calendar"></div>

<!-- Upcoming Trip Details Section -->
<div class="trip-details">
    <h3>આવનારી ટ્રિપ (આવતા ૭ દિવસ)</h3>
    <table>
        <thead>
            <tr>
                <th>ટ્રિપ</th>
                <th>તારીખ</th>
                <th>રકમ</th>
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
                <tr><td colspan="3" class="no-trips">No upcoming trips found.</td></tr>
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
                // Add smooth fade-in effect on event render
                element.hide().fadeIn(1000);
            },
            eventClick: function (event) {
                alert('Trip: ' + event.title + '\nDate: ' + event.start.format('YYYY-MM-DD') + '\n' + event.description);
            }
        });

        // Add smooth animation to the upcoming trips table
        $('table tbody tr').each(function(index) {
            $(this).delay(index * 300).fadeIn(1000);
        });
    });
</script>

</body>
</html>
