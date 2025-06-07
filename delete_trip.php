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
    echo "<script>alert('ટ્રિપ સફળતાપૂર્વક નીકળી ગઈ છે'); window.location.href='trip_manage.php';</script>";
} else {
    echo "<script>alert('ટ્રિપ ડિલીટ કરવામાં નિષ્ફળ થયાં'); window.location.href='trip_manage.php';</script>";
}
$stmt->close();
