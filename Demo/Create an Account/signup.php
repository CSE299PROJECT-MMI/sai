<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $role = $_POST['role'];
    $department = $_POST['department'] ?? null; // Only applicable for doctors

    // Check if email exists in both tables
    $checkEmail = "SELECT email FROM users WHERE email = ? UNION SELECT email FROM doctors WHERE email = ?";
    $stmt = $conn->prepare($checkEmail);
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errorMessage = "An account with this email already exists.";
    } else {
        if ($role === "doctor") {
            // Insert into doctors table
            $sqlDoctor = "INSERT INTO doctors (first_name, last_name, phone_number, email, password, birthday, gender, department) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtDoctor = $conn->prepare($sqlDoctor);
            $stmtDoctor->bind_param("ssssssss", $first_name, $last_name, $phone_number, $email, $password, $birthday, $gender, $department);

            if ($stmtDoctor->execute() === TRUE) {
                $_SESSION['user_id'] = $email;
                $_SESSION['role'] = 'doctor';
                header("Location: login.php");
                exit();
            } else {
                $errorMessage = "Error: " . $stmtDoctor->error;
            }
            $stmtDoctor->close();
        } else {
            // Insert into users table
            $sqlUser = "INSERT INTO users (first_name, last_name, phone_number, email, password, birthday, gender, role) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtUser = $conn->prepare($sqlUser);
            $stmtUser->bind_param("ssssssss", $first_name, $last_name, $phone_number, $email, $password, $birthday, $gender, $role);

            if ($stmtUser->execute() === TRUE) {
                $_SESSION['user_id'] = $email;
                $_SESSION['role'] = $role;
                header("Location: login.php");
                exit();
            } else {
                $errorMessage = "Error: " . $stmtUser->error;
            }
            $stmtUser->close();
        }
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an Account</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Create an Account</h2>
        <?php if (!empty($errorMessage)): ?>
            <p style="color: red;"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <form method="post" action="">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="birthday">Birthday:</label>
            <input type="date" id="birthday" name="birthday" required>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <label for="role">User Role:</label>
            <select id="role" name="role" onchange="toggleDepartmentField()" required>
                <option value="patient">Patient</option>
                <option value="doctor">Doctor</option>
                <option value="admin">Admin</option>
            </select>

            <div id="department-field" style="display: none;">
    <label for="department">Department:</label>
    <select id="department" name="department" required>
        <option value="" disabled selected>Select Department</option>
        <option value="Psychosexual Disorder Clinic">Psychosexual Disorder Clinic</option>
        <option value="Addiction Clinic">Addiction Clinic</option>
        <option value="Psychotherapy Clinic">Psychotherapy Clinic</option>
        <option value="Depression Clinic">Depression Clinic</option>
        <option value="Sex Therapy Clinic">Sex Therapy Clinic</option>
        <option value="Drug Addiction Clinic">Drug Addiction Clinic</option>
        <option value="Headache Clinic">Headache Clinic</option>
        <option value="OCD Clinic">OCD Clinic</option>
        <option value="Geriatric Clinic">Geriatric Clinic</option>
        <option value="Neurotic Mental Health Clinic">Neurotic Mental Health Clinic</option>
    </select>
</div>

            <button type="submit">Create Account</button>
        </form>
    </div>

    <script>
        function toggleDepartmentField() {
            const role = document.getElementById("role").value;
            const departmentField = document.getElementById("department-field");
            if (role === "doctor") {
                departmentField.style.display = "block";
            } else {
                departmentField.style.display = "none";
            }
        }
    </script>
</body>
</html>
