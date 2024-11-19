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

// Retrieve the appointment_id from the URL
if (!isset($_GET['appointment_id'])) {
    die("Appointment ID not provided");
}

$appointment_id = $_GET['appointment_id'];

// Fetch appointment details
$sql = "SELECT a.*, p.name AS patient_name, p.phone_number, p.birthday, p.gender 
        FROM appointments a
        JOIN patients p ON a.patient_id = p.patient_id
        WHERE a.appointment_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$appointment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$appointment) {
    die("Appointment not found");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <main class="content">
            <h1>Appointment Details</h1>
            <section class="appointment-details">
                <h2>Patient Information</h2>
                <p><strong>Name:</strong> <?php echo $appointment['patient_name']; ?></p>
                <p><strong>Phone:</strong> <?php echo $appointment['phone_number']; ?></p>
                <p><strong>Birthday:</strong> <?php echo $appointment['birthday']; ?></p>
                <p><strong>Gender:</strong> <?php echo $appointment['gender']; ?></p>

                <h2>Appointment Information</h2>
                <p><strong>Date:</strong> <?php echo $appointment['date']; ?></p>
                <p><strong>Start Time:</strong> <?php echo $appointment['start_time']; ?></p>
                <p><strong>End Time:</strong> <?php echo $appointment['end_time']; ?></p>
                <p><strong>Status:</strong> <?php echo ucfirst($appointment['status']); ?></p>
            </section>
        </main>

        <!-- Right Sidebar -->
        <aside class="sidebar right">
            <button onclick="window.location.href='appointments.php'">Back to Appointments</button>
        </aside>
    </div>
</body>
</html>
