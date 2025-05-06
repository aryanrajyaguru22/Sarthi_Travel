<!-- delete_bus.php -->

<?php
include 'db.php';
$id = $_GET['id'];
$conn->query("DELETE FROM buses WHERE id=$id");
header("Location: add_bus.php");
?>
