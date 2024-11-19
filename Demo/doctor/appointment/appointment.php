<?php
session_start();

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../Create an Account/login.php");
    exit();
}
// Set the correct timezone to ensure accurate date and time
date_default_timezone_set('Asia/Dhaka'); // Replace 'Asia/Dhaka' with your timezone

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve doctor details including gender
$email = $_SESSION['user_id'];
$sql = "SELECT first_name, last_name, gender FROM doctors WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$stmt->close();

// Today's date
$date = date('Y-m-d');

// Retrieve appointments for today
$appointments_sql = "
    SELECT a.patient_name, a.start_time, a.end_time, a.status
    FROM appointments a
    WHERE a.doctor_email = ? AND a.appointment_date = ?";

$appointments_stmt = $conn->prepare($appointments_sql);
$appointments_stmt->bind_param("ss", $email, $date);
$appointments_stmt->execute();
$appointments_result = $appointments_stmt->get_result();


// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor's Appointments</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <!-- Main Content -->
        <main class="content">
            <h1>Today's Appointments</h1>
            <section class="appointments">
                <h2>Appointments for Dr. <?php echo htmlspecialchars($doctor['first_name'] . " " . $doctor['last_name']); ?></h2>
                <p><strong>Date:</strong> <?php echo $date; ?></p>
                <table>
                    <caption>Today's Appointments</caption>
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($appointment = $appointments_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['start_time']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['end_time']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($appointment['status'])); ?></td>
                                <td>
    <a href="#">View</a> | 
    <a href="#">Mark as Done</a> | 
    <a href="#">&status=rescheduled">Reschedule</a>
</td>

                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        </main>

        <!-- Sidebar -->
        <aside class="sidebar right">
            <!-- Profile Section -->
            <div class="profile" onclick="window.location.href='../profile/doctor_profile.php';" style="cursor: pointer;">
                <?php 
                // Display profile image based on gender with a fallback to default
                $profileImage = '../doctor/img/default.png'; // Default image
                if (isset($doctor['gender'])) {
                    if ($doctor['gender'] === 'Male') {
                        $profileImage = '../photo/male.png';
                    } elseif ($doctor['gender'] === 'Female') {
                        $profileImage = '../photo/female.png';
                    }
                }
                ?>
                <img src="<?php echo $profileImage; ?>" alt="Profile Picture">

                <p><?php echo isset($doctor['first_name']) && isset($doctor['last_name']) ? htmlspecialchars($doctor['first_name'] . " " . $doctor['last_name']) : "Doctor"; ?></p>
            </div>

            <!-- Navigation Links -->
            <nav>
                <ul>
                    <li><a href="../doctor_dashboard.php">Dashboard</a></li>
                    <li><a href="../availability/availability.php">Availability</a></li>
                    <li><a href="../appointment/view_appointment.php">Appointments View</a></li>
                </ul>
            </nav>
        </aside>
    </div>
</body>
</html>
