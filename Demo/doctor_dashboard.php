<?php
session_start();

// Check if user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
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
$schedule_sql = "SELECT slot_id, date, start_time, end_time, status FROM availability_slots WHERE doctor_email=?";
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
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Doctor Dashboard</h2>
    <form method="post" action="set_availability.php">
        <label>Date: <input type="date" name="date" required></label>
        <label>Start Time: <input type="time" name="start_time" required></label>
        <label>End Time: <input type="time" name="end_time" required></label>
        <button type="submit">Set Availability</button>
    </form>

    <h3>Your Schedule</h3>
    <table>
        <tr>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while($row = $schedule_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['date']); ?></td>
            <td><?php echo htmlspecialchars($row['start_time']); ?></td>
            <td><?php echo htmlspecialchars($row['end_time']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>
                <?php if ($row['status'] === 'available'): ?>
                    <form method="post" action="cancel_availability.php">
                        <input type="hidden" name="slot_id" value="<?php echo $row['slot_id']; ?>">
                        <button type="submit">Cancel</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
