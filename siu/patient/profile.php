<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../Create an Account/login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve doctor details from the doctors table
$email = $_SESSION['user_id']; // Doctor's email is stored in the session

// SQL query to fetch doctor details
$sql = "
SELECT 
    d.email, 
    d.first_name, 
    d.last_name, 
    d.phone_number, 
    d.birthday, 
    d.gender, 
    d.profile_picture, 
    de.school, 
    de.college, 
    de.medical_college, 
    de.other_degrees, 
    de.father_name, 
    de.mother_name, 
    de.address, 
    de.specialties
FROM 
    doctors d
LEFT JOIN 
    doctor_edu de ON d.id = de.user_id
WHERE 
    d.email = ?;
";

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Assign defaults for missing fields
$profilePicture = $doctor['profile_picture'] ?? ($doctor['gender'] === 'Male' ? 'Male.png' : 'Female.png');
$firstName = $doctor['first_name'] ?? "N/A";
$lastName = $doctor['last_name'] ?? "N/A";
$email = $doctor['email'] ?? "N/A";
$phoneNumber = $doctor['phone_number'] ?? "N/A";
$birthday = $doctor['birthday'] ?? "N/A";
$gender = $doctor['gender'] ?? "N/A";
$school = $doctor['school'] ?? "Not provided";
$college = $doctor['college'] ?? "Not provided";
$medicalCollege = $doctor['medical_college'] ?? "Not provided";
$otherDegrees = $doctor['other_degrees'] ?? "N/A";
$fatherName = $doctor['father_name'] ?? "N/A";
$motherName = $doctor['mother_name'] ?? "N/A";
$address = $doctor['address'] ?? "N/A";

// Check if the user is a doctor and prepend "Dr." to the name
$fullName = "Dr. " . $firstName . " " . $lastName;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile</title>
    <link rel="stylesheet" href="pro.css">
</head>
<body>
    <div class="profile-container">
        <h1>Welcome to Doctor Profile</h1>
        <div class="profile">
            <div class="left-side">
                <img src="../doctor/img/<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture">
                <h2><?php echo htmlspecialchars($fullName); ?></h2>
                <p><strong>Specialties:</strong> <?php echo htmlspecialchars($doctor['specialties'] ?? "Not provided"); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <p><strong>Educations:</strong></p>
                <p><?php echo htmlspecialchars($school); ?></p>
                <p><?php echo htmlspecialchars($college); ?></p>
                <p><?php echo htmlspecialchars($medicalCollege); ?></p>
                <p><strong>Degrees:</strong></p>
                <p><em></em> <?php echo htmlspecialchars($otherDegrees); ?></p>
            </div>
            <div class="right-side">
                <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($phoneNumber); ?></p>
                <p><strong>Birthday:</strong> <?php echo htmlspecialchars($birthday); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($gender); ?></p>
                <p><strong>Father's Name:</strong> <?php echo htmlspecialchars($fatherName); ?></p>
                <p><strong>Mother's Name:</strong> <?php echo htmlspecialchars($motherName); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
            </div>
        </div>
        <div class="button_container">
            <button onclick="window.location.href='edit_profile.php'">Edit Profile</button>
            <button onclick="window.location.href='../doctor_dashboard.php'">Dashboard</button>
        </div>
    </div>
</body>
</html> 