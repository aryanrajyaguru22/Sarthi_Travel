 <!-- delete_trip.php -->

 <?php
include 'db.php';
session_start();

if (!isset($_GET['id'])) {
    echo "<script>alert('Invalid trip ID'); window.location.href='trip_manage.php';</script>";
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM trip_details WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('Trip deleted successfully'); window.location.href='trip_manage.php';</script>";
} else {
    echo "<script>alert('Failed to delete trip'); window.location.href='trip_manage.php';</script>";
}
$stmt->close();
