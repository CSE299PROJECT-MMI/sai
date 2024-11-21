<?php
session_start();

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../Create an Account/login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the start_time and end_time from the URL
if (!isset($_GET['start_time']) || !isset($_GET['end_time'])) {
    die("Start time or end time not provided");
}

$start_time = $_GET['start_time'];
$end_time = $_GET['end_time'];

// Delete the availability slot from the database
$delete_sql = "DELETE FROM doctor_availability WHERE start_time = ? AND end_time = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("ss", $start_time, $end_time);
$delete_stmt->execute();

if ($delete_stmt->affected_rows > 0) {
    echo "Availability slot deleted successfully.";
} else {
    echo "No matching availability slot found.";
}

$delete_stmt->close();
$conn->close();

// Redirect back to the availability page
header("Location: availability.php");
exit();
?>