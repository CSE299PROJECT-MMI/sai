<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['username']; // changed to 'username' to match the form field name
    $password = $_POST['password'];

    // Prepare and execute the SQL query
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['email'];
            $_SESSION['role'] = $row['role'];

            // Redirect based on role
            if ($row['role'] === 'admin') {
                header("Location: admin_dashboard.php");
                exit();
            } elseif ($row['role'] === 'doctor') {
                header("Location: doctor_dashboard.php");
                exit();
            } elseif ($row['role'] === 'patient') {
                header("Location: ../patient/patient_dashboard.php");
                exit();
            } else {
                $errorMessage = "Invalid role.";
            }
        } else {
            $errorMessage = "Invalid password!";
        }
    } else {
        $errorMessage = "User not found!";
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
