<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../Create an Account/login.php");
    exit();
}

// Get the email from the session or the query string
$email = isset($_GET['email']) ? $_GET['email'] : $_SESSION['user_id'];

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $patient_status = $_POST['patient_status'];
    $department_name = $_POST['department_name'];
    $doctor_name = $_POST['doctor_name'];

    // Insert appointment into the database
    $sql = "INSERT INTO appointments (email, name, phone_number, age, gender, address, appointment_date, appointment_time, patient_status, department_name, doctor_name) 
            VALUES ('$email', '$name', '$phone_number', $age, '$gender', '$address', '$appointment_date', '$appointment_time', '$patient_status', '$department_name', '$doctor_name')";

    if ($conn->query($sql) === TRUE) {
        header("Location: confirmation.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Appointment Booking</title>
    <link rel="stylesheet" href="CSS\styles.css">
</head>
<body>
    <div class="appointment-form">
        <h2>Book an Appointment</h2>
        <form method="post" action="">
        <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly required>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required>

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" required>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>

            <label for="appointment_date"> Appointment Date:</label>
            <input type="date" id="appointment_date" name="appointment_date" required>

            <label for="appointment_time">Visiting Time:</label>
            <input type="time" id="appointment_time" name="appointment_time" required>

            <label for="patient_status">Patient Status:</label>
            <select id="patient_status" name="patient_status" required>
                <option value="New">New</option>
                <option value="Old">Old</option>
            </select>

            <label for="department_name">Department:</label>
            <select id="department_name" name="department_name" required onchange="populateDoctors(this.value)">
                <option value="">Select Department</option>
                <option value="Child and Adolescent Development Clinic">Child and Adolescent Development Clinic</option>
                <option value="Psychosexual Disorder Clinic">Psychosexual Disorder Clinic</option>
                <option value="Addiction Clinic">Addiction Clinic</option>
                <option value="Psychotherapy Clinic">Psychotherapy Clinic</option>
                <option value="Depression Clinic">Depression Clinic</option>
                <option value="Sex Therapy Clinic">Sex Therapy Clinic</option>
                <option value="Drug Addiction Clinic">Drug Addiction Clinic</option>
                <option value="Headache Clinic">Headache Clinic</option>
                <option value="OCD (Obsessive-Compulsive Disorder) Clinic">OCD (Obsessive-Compulsive Disorder) Clinic</option>
                <option value="Geriatric Clinic">Geriatric Clinic</option>
                <option value="Neurotic Mental Health Clinic">Neurotic Mental Health Clinic</option>
            </select>

            <label for="doctor_name">Doctor Name:</label>
            <select id="doctor_name" name="doctor_name" required>
                <option value="">Select Doctor</option>
                <!-- Options will be populated by JavaScript -->
            </select>

            <button type="submit">Book Appointment</button>
        </form>
    </div>

    <script src="script.js"></script>
</body>
</html>
