
<?php
session_start();

// Check if the user is logged in and has the role of 'patient'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../Create an Account/login.php");
    exit();
}

// Get the email from the session or query string
$email = isset($_GET['email']) ? $_GET['email'] : $_SESSION['user_id'];

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_appointment'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $age = (int)$_POST['age'];
    $gender = $conn->real_escape_string($_POST['gender']);
    $address = $conn->real_escape_string($_POST['address']);
    $appointment_date = $conn->real_escape_string($_POST['appointment_date']);
    $start_time = $conn->real_escape_string($_POST['appointment_time']);
    $end_time = date("H:i:s", strtotime('+1 hour', strtotime($start_time)));
    $patient_status = $conn->real_escape_string($_POST['patient_status']);
    $department_name = $conn->real_escape_string($_POST['department_name']);
    $doctor_name = $conn->real_escape_string($_POST['doctor_name']);

    // Get doctor's email
    $doctor_query = "SELECT email FROM doctors WHERE CONCAT(first_name, ' ', last_name) = '$doctor_name'";
    $doctor_result = $conn->query($doctor_query);
    $doctor_email = '';
    if ($doctor_result->num_rows > 0) {
        $doctor_row = $doctor_result->fetch_assoc();
        $doctor_email = $doctor_row['email'];
    } else {
        echo "Error: Doctor not found.";
        exit();
    }
// Check if the doctor is available by checking both the appointments and doctor_availability tables
$doctor_check_sql = "
    SELECT doctor_name, appointment_date, start_time, end_time 
    FROM appointments
    WHERE doctor_name = ? 
      AND appointment_date = ? 
      AND (? < end_time AND ? > start_time)
    UNION
    SELECT doctor_email AS doctor_name, available_date AS appointment_date, start_time, end_time
    FROM doctor_availability
    WHERE doctor_email = ? 
      AND available_date = ? 
      AND (? < end_time AND ? > start_time)";

// Prepare the statement
$stmt = $conn->prepare($doctor_check_sql);
$stmt->bind_param("ssssssss", $doctor_name, $appointment_date, $start_time, $end_time, $doctor_email, $appointment_date, $start_time, $end_time);

// Execute the query
$stmt->execute();
$doctor_result = $stmt->get_result();

// If the doctor has any conflicting appointments or availability, show a message
if ($doctor_result->num_rows > 0) {
    header("Location: doctorcheck.php"); // Doctor is unavailable
    exit();
}



    // Check if the patient already has an appointment
    $patient_check_sql = "
        SELECT * FROM appointments_patients 
                          WHERE email = '$email' 
                            AND appointment_date = '$appointment_date' 
                            AND ('$start_time' < end_time AND ADDTIME('$start_time', '01:00:00') > start_time)";
    $patient_result = $conn->query($patient_check_sql);

    if ($patient_result->num_rows > 0) {
        header("Location: check.php"); // Patient already has an appointment
        exit();
    }

    // Insert appointment into appointments_patients table
    $insert_sql_patient = "
    INSERT INTO appointments_patients (email, name, phone_number, age, gender, address, appointment_date, start_time, patient_status, department_name, doctor_name) 
    VALUES ('$email', '$name', '$phone_number', $age, '$gender', '$address', '$appointment_date', '$start_time', '$patient_status', '$department_name', '$doctor_name')";

    if ($conn->query($insert_sql_patient) === TRUE) {
        // After booking the appointment, retrieve the patient_id from appointments_patients table
        $patient_id_query = "SELECT id FROM appointments_patients WHERE email = '$email' ORDER BY id DESC LIMIT 1";
        $patient_id_result = $conn->query($patient_id_query);
        $patient_id = '';
        if ($patient_id_result->num_rows > 0) {
            $patient_row = $patient_id_result->fetch_assoc();
            $patient_id = $patient_row['id'];
        } else {
            echo "Error: Could not retrieve patient ID.";
            exit();
        }

        // Insert into appointments table with the retrieved patient_id
        $insert_confirmation_sql = "
        INSERT INTO appointments (doctor_email, patient_id, patient_name, appointment_date, start_time, end_time, status, doctor_name) 
        VALUES ('$doctor_email', '$patient_id', '$name', '$appointment_date', '$start_time', '$end_time', 'Confirmed', '$doctor_name')";

        if ($conn->query($insert_confirmation_sql) === TRUE) {
            header("Location: confirmation.php"); // Booking confirmed
            exit();
        } else {
            echo "Error: " . $insert_confirmation_sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error: " . $insert_sql_patient . "<br>" . $conn->error;
    }
}

// Populate departments
$departments = [];
if ($result = $conn->query("SELECT DISTINCT department FROM doctors")) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row['department'];
    }
}

// Populate doctors based on the selected department
$doctors = [];
$selected_department = isset($_POST['department_name']) ? $conn->real_escape_string($_POST['department_name']) : '';
if ($selected_department) {
    $doctor_query = "SELECT first_name, last_name FROM doctors WHERE department = '$selected_department'";
    $doctor_result = $conn->query($doctor_query);

    while ($row = $doctor_result->fetch_assoc()) {
        $doctors[] = $row['first_name'] . ' ' . $row['last_name'];
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
    <title>Book an Appointment</title>
    <link rel="stylesheet" href="CSS/styles.css">
</head>
<body>
<div class="appointment-form">
    <h2>Book an Appointment</h2>
    <form method="post" action="">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly required>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>" required>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''; ?>" required>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
            <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
            <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
        </select>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>" required>

        <label for="appointment_date">Appointment Date:</label>
        <input type="date" id="appointment_date" name="appointment_date" value="<?php echo isset($_POST['appointment_date']) ? htmlspecialchars($_POST['appointment_date']) : ''; ?>" required>

        <label for="appointment_time">Start Time:</label>
        <input type="time" id="appointment_time" name="appointment_time" value="<?php echo isset($_POST['appointment_time']) ? htmlspecialchars($_POST['appointment_time']) : ''; ?>" required>

        <label for="patient_status">Patient Status:</label>
        <select id="patient_status" name="patient_status" required>
            <option value="New" <?php echo (isset($_POST['patient_status']) && $_POST['patient_status'] === 'New') ? 'selected' : ''; ?>>New</option>
            <option value="Returning" <?php echo (isset($_POST['patient_status']) && $_POST['patient_status'] === 'Returning') ? 'selected' : ''; ?>>Returning</option>
        </select>

        <label for="department_name">Department:</label>
        <select id="department_name" name="department_name" required onchange="this.form.submit()">
            <option value="">Select Department</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo $department; ?>" <?php echo (isset($_POST['department_name']) && $_POST['department_name'] === $department) ? 'selected' : ''; ?>>
                    <?php echo $department; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="doctor_name">Doctor:</label>
        <select id="doctor_name" name="doctor_name" required>
            <option value="">Select Doctor</option>
            <?php foreach ($doctors as $doctor): ?>
                <option value="<?php echo $doctor; ?>" <?php echo (isset($_POST['doctor_name']) && $_POST['doctor_name'] === $doctor) ? 'selected' : ''; ?>>
                    <?php echo $doctor; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="book_appointment">Book Appointment</button>
    </form>
</div>
</body>
</html>
