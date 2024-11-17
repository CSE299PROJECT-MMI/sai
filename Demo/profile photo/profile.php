<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Create an Account/login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle photo upload if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $fileTmpPath = $_FILES['profile_image']['tmp_name'];
    $fileName = $_FILES['profile_image']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    // Validate file type and size
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        echo "<script>alert('Invalid file type. Only JPG, JPEG, and PNG are allowed.');</script>";
    } elseif ($_FILES['profile_image']['size'] > $maxFileSize) {
        echo "<script>alert('File size exceeds the 2MB limit.');</script>";
    } else {
        // Set upload directory
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique file name
        $newFileName = uniqid('profile_', true) . '.' . $fileExtension;
        $uploadPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $uploadPath)) {
            // Update database
            $email = $_SESSION['user_id'];
            $sql = "UPDATE users SET profile_pic = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $newFileName, $email);

            if ($stmt->execute()) {
                echo "<script>alert('Profile photo updated successfully!');</script>";
            } else {
                echo "<script>alert('Error updating profile photo.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Error uploading the file.');</script>";
        }
    }
}

// Fetch user details
$email = $_SESSION['user_id'];
$sql = "SELECT profile_pic, first_name, last_name FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Default profile picture if none is uploaded
$profilePic = $user['profile_pic'] ? "../uploads/" . $user['profile_pic'] : "ph/download.png";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Photo</title>
    <style>
        *{
    margin: 0;
    padding: 0;
    font-family: 'Poppins',sans-serif;
    box-sizing: border-box;
}
.profile{
    width: 100%;
    height: 100vh;
    background: #d1d1d1;
    display: flex;
    align-items: center;
    justify-content: center;

}
.pic{
    width: 400px;
    background: #fff;
    padding: 40px;
    border-radius: 15px;
    text-align: center;
    color: #333;
}
.pic h1{
    font-weight: 500;
    color: #000;
}
.pic img{
    width: 180px;
    height: 180px;
    border-radius: 50%;
    margin-top: 40px;
    margin-bottom: 30px;

}
label{
    display: block;
    width: 200px;
    background:#e3362c;
    color: #fff;
    padding: 12px;
    border-radius: 5px;
    margin: 10px auto;
    cursor: pointer;
}
input{
    display: none;
}
    </style>
</head>
<body>
<div class="profile">
        <div class="pic">
            <h1><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
            <p>@<?php echo htmlspecialchars($email); ?></p>
            <img src="<?php echo htmlspecialchars($profilePic); ?>" id="profile-pic">
            
            <!-- Form for uploading a new photo -->
            <form action="../profile .php" method="POST" enctype="multipart/form-data">

                <label for="input-file">Update Image</label>
                <input type="file" name="profile_image" accept="image/png, image/jpeg, image/jpg" id="input-file" required>
                <button type="submit">Upload</button>
            </form>
        </div>
    </div>
    <script>
        let profilepic = document.getElementById("profile-pic");
        let inputFile = document.getElementById("input-file");
        inputFile.onchange =function(){
            profilepic.src=URL.createObjectURL(inputFile.files[0]);
        }
           </script>

</body>
</html>
