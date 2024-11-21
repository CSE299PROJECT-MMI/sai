<?php
session_start();

// Check if user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location:../Create an Account/login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve doctor details
$email = $_SESSION['user_id'];
$sql = "SELECT first_name, last_name, phone_number, birthday, gender FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$stmt->close();

// Retrieve doctor's schedule
$schedule_sql = "SELECT id, available_date, start_time, end_time, doctor_name FROM doctor_availability WHERE doctor_email=?";
$schedule_stmt = $conn->prepare($schedule_sql);
$schedule_stmt->bind_param("s", $email);
$schedule_stmt->execute();
$schedule_result = $schedule_stmt->get_result();

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <!-- Add FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <nav>
            <!-- Admin Dashboard Title -->
            <h2>Admin Dashboard</h2>
            
            <ul>
                <li><a href="#overview">Overview</a></li>
                <li><a href="#user-management">User Management</a></li>
                <li><a href="#appointment-management">Appointment Management</a></li>
                <li><a href="#payment-management">Payment Management</a></li>
                <li><a href="#reports-analytics">Reports & Analytics</a></li>
                <li><a href="#system-settings">System Settings</a></li>
                <li><a href="#notifications">Notifications</a></li>
                <li><a href="#feedback">Feedback</a></li>
            </ul>

            <!-- Logout Icon -->
            <aside>    
                <button onclick="logout()" class="logout-button">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </aside>
        </nav>

        <div class="dashboard-content">
            <section id="overview">
                <h3>Overview</h3>
                <p>Summary of activities, pending appointments, and outstanding payments.</p>
            </section>

            <section id="user-management">
                <h3>User Management</h3>
                <p>Manage users, activate/deactivate accounts, edit user details.</p>
            </section>

            <section id="appointment-management">
                <h3>Appointment Management</h3>
                <p>Approve or deny appointment requests, view schedules, reassign counselors.</p>
            </section>

            <section id="payment-management">
                <h3>Payment Management</h3>
                <p>Track and update payment statuses, view histories, generate financial reports.</p>
            </section>

            <section id="reports-analytics">
                <h3>Reports & Analytics</h3>
                <p>Monitor KPIs, generate reports on user activity, appointments, financial performance.</p>
            </section>

            <section id="system-settings">
                <h3>System Settings</h3>
                <p>Manage site settings, update system info, oversee application maintenance.</p>
            </section>

            <section id="notifications">
                <h3>Notifications</h3>
                <p>Send notifications and alerts about appointment updates or critical issues.</p>
            </section>

            <section id="feedback">
                <h3>Feedback</h3>
                <p>Review and respond to user feedback, initiate communication if needed.</p>
            </section>
        </div>
    </div>

    <script>
        function logout() {
            window.location.href = '../Create an Account/logout.php';
        }
    </script>
</body>
</html>
