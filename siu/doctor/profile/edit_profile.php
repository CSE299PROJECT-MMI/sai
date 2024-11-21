<?php
session_start();

// Check if user is logged in as a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../Create an Account/login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch doctor data
$email = $_SESSION['user_id'];
$sql = "SELECT d.email, d.first_name, d.last_name, d.phone_number, d.birthday, d.gender, 
               de.profile_picture, de.school, de.college, de.medical_college, de.other_degrees, 
               de.father_name, de.mother_name, de.address, de.specialties
        FROM doctors d
        LEFT JOIN doctor_edu de ON d.id = de.user_id
        WHERE d.email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $birthday = htmlspecialchars(trim($_POST['birthday']));
    $school = htmlspecialchars(trim($_POST['school']));
    $college = htmlspecialchars(trim($_POST['college']));
    $medical_college = htmlspecialchars(trim($_POST['medical_college']));
    $other_degrees = htmlspecialchars(trim($_POST['other_degrees']));
    $father_name = htmlspecialchars(trim($_POST['father_name']));
    $mother_name = htmlspecialchars(trim($_POST['mother_name']));
    $address = htmlspecialchars(trim($_POST['address']));
    $specialties = htmlspecialchars(trim($_POST['specialties']));

    // Handle profile picture upload
    $profile_picture = $doctor['profile_picture'];
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $upload_dir = "../doctor/img/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        if (in_array($file_extension, $allowed_extensions) && $_FILES['profile_picture']['size'] <= 2 * 1024 * 1024) {
            $file_name = uniqid() . "." . $file_extension;
            $file_path = $upload_dir . basename($file_name);
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_path)) {
                $profile_picture = $file_name;
            }
        }
    }

    // Update `users` table
    $updateUsersSql = "UPDATE users SET first_name=?, last_name=?, phone_number=?, birthday=? WHERE email=?";
    $stmt = $conn->prepare($updateUsersSql);
    $stmt->bind_param("sssss", $first_name, $last_name, $phone_number, $birthday, $_SESSION['user_id']);
    if (!$stmt->execute()) {
        die("Error updating user data: " . $stmt->error);
    }

    // Update `doctor_edu` table
    $updateDoctorEduSql = "
    INSERT INTO doctor_edu (user_id, profile_picture, school, college, medical_college, other_degrees, father_name, mother_name, address, specialties)
    VALUES ((SELECT id FROM users WHERE email=?), ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE profile_picture=?, school=?, college=?, medical_college=?, other_degrees=?, father_name=?, mother_name=?, address=?, specialties=?";
    $stmt = $conn->prepare($updateDoctorEduSql);
    $stmt->bind_param(
        "sssssssssssssssssss", 
        $email, $profile_picture, $school, $college, $medical_college, $other_degrees, 
        $father_name, $mother_name, $address, $specialties,
        $profile_picture, $school, $college, $medical_college, $other_degrees, 
        $father_name, $mother_name, $address, $specialties
    );
    if (!$stmt->execute()) {
        die("Error updating doctor education data: " . $stmt->error);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Update session and redirect
    $_SESSION['user_id'] = $email;
    header("Location: doctor_profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Doctor Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Edit Doctor Profile</h1>
    <form method="POST" enctype="multipart/form-data">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($doctor['first_name']); ?>" required>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($doctor['last_name']); ?>" required>

        <label for="phone_number">Phone Number:</label>
        <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($doctor['phone_number']); ?>" required>

        <label for="birthday">Birthday:</label>
        <input type="date" id="birthday" name="birthday" value="<?php echo htmlspecialchars($doctor['birthday']); ?>" required>

        <label for="profile_picture">Profile Picture:</label>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">

        <label for="school">School:</label>
        <input type="text" id="school" name="school" value="<?php echo htmlspecialchars($doctor['school']); ?>">

        <label for="college">College:</label>
        <input type="text" id="college" name="college" value="<?php echo htmlspecialchars($doctor['college']); ?>">

        <label for="medical_college">Medical College:</label>
        <input type="text" id="medical_college" name="medical_college" value="<?php echo htmlspecialchars($doctor['medical_college']); ?>">

        <label for="other_degrees">Other Degrees:</label>
        <input type="text" id="other_degrees" name="other_degrees" value="<?php echo htmlspecialchars($doctor['other_degrees']); ?>">

        <label for="father_name">Father's Name:</label>
        <input type="text" id="father_name" name="father_name" value="<?php echo htmlspecialchars($doctor['father_name']); ?>">

        <label for="mother_name">Mother's Name:</label>
        <input type="text" id="mother_name" name="mother_name" value="<?php echo htmlspecialchars($doctor['mother_name']); ?>">

        <label for="address">Address:</label>
        <textarea id="address" name="address"><?php echo htmlspecialchars($doctor['address']); ?></textarea>

        <label for="specialties">Specialties:</label>
        <input type="text" id="specialties" name="specialties" value="<?php echo htmlspecialchars($doctor['specialties']); ?>">

        <button type="submit">Save Changes</button>
    </form>
    <button onclick="window.location.href='doctor_profile.php'">Back to Profile</button>
</body>
</html>
