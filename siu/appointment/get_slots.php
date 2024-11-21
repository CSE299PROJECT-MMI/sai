<?php
// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hospital_management"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$doctorName = isset($_GET['doctorName']) ? $_GET['doctorName'] : '';
$selectedDate = isset($_GET['date']) ? $_GET['date'] : '';

// Initialize time slots array
$timeSlots = [];

// Fetch time slots based on the selected date
if (!empty($doctorName) && !empty($selectedDate)) {
    // Query to fetch start_time and end_time for the selected doctor and date
    $sql = "SELECT start_time, end_time FROM doctor_availability WHERE doctor_name = ? AND available_date = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $doctorName, $selectedDate);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $startTime = strtotime($row['start_time']);
                $endTime = strtotime($row['end_time']);
                $currentDate = date('Y-m-d'); // Get today's date
                $currentTime = time(); // Get current time

                // Check if end time is past midnight
                if ($endTime < $startTime) {
                    $endTime = strtotime('+1 day', $endTime); // Adjust end time to next day
                }

                // Generate 1-hour time slots
                while ($startTime < $endTime) {
                    if ($selectedDate > $currentDate || ($selectedDate == $currentDate && $startTime > $currentTime)) {
                        // Add time slot if it's in the future
                        $timeSlots[] = date('h:i A', $startTime);
                    }
                    $startTime = strtotime('+1 hour', $startTime); // Increment by 1 hour
                }
            }
        } else {
            echo "Error executing query: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close(); // Close the connection after the operation

// Return time slots as JSON
if (empty($timeSlots)) {
    echo json_encode(['message' => 'No slots available for today.']);
} else {
    echo json_encode(['timeSlots' => $timeSlots]);
}
?>
