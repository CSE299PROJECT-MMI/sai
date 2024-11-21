<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['username'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errorMessage = "Please enter both email and password.";
    } else {
        // Check if the email exists in the users table
        $sql = "SELECT * FROM users WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['email'];
                $_SESSION['role'] = 'patient'; // Role is set as 'patient'

                // Redirect to the patient dashboard
                header("Location: ../patient/patient_dashboard.php");
                exit();
            } else {
                $errorMessage = "Invalid password!";
            }
        } else {
            // If not found in users, check the doctors table
            $sql = "SELECT * FROM doctors WHERE email=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['email'];
                    $_SESSION['role'] = 'doctor'; // Role is set as 'doctor'

                    // Redirect to the doctor dashboard
                    header("Location: ../doctor/doctor_dashboard.php");
                    exit();
                } else {
                    $errorMessage = "Invalid password!";
                }
            } else {
                // If not found in users or doctors, check the admins table
                $sql = "SELECT * FROM admins WHERE email=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if (password_verify($password, $row['password'])) {
                        $_SESSION['user_id'] = $row['email'];
                        $_SESSION['role'] = 'admin'; // Role is set as 'admin'

                        // Redirect to the admin dashboard
                        header("Location: ../admin/admin_dashboard.php");
                        exit();
                    } else {
                        $errorMessage = "Invalid password!";
                    }
                } else {
                    $errorMessage = "User not found!";
                }
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        <form method="post" action="">
            <label for="username">Email:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <?php if ($errorMessage): ?>
            <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <p><a href="forgot_password.php">Forgot Password?</a></p>
        <p>Don't have an account? <a href="signup.php">Register here</a></p>
        <p><a href="../index.html">HOME PAGE</a></p>
    </div>
</body>
</html>
