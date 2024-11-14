<?php
session_start();

$host = 'localhost';
$db = 'task_master';
$user = 'root';  // your MySQL username
$pass = '';      // your MySQL password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if password and confirm password match
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        // Check if username or email already exists
        $sql = "SELECT * FROM users_admin WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Username or email already exists.');</script>";
        } else {
            // Hash the password and insert new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users_admin (email, username, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $email, $username, $hashedPassword);

            if ($stmt->execute()) {
                echo "<script>alert('Registration successful!'); window.location.href='alogin.php';</script>";
            } else {
                echo "<script>alert('Error registering user. Please try again.');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Register</title>
</head>
<body>
    <div class="login-container">
        <h2>Admin Register</h2>
        <form method="POST" action="aregister.php" onsubmit="return validateForm()">
            <div class="register-form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required 
                    pattern="^[a-zA-Z0-9._%+-]+@+.com$" 
                    title="Email must be in the format of yourname@examplemail.com">

                <label>Username</label>
                <input type="text" name="username" placeholder="Enter your username" required 
                       minlength="8" maxlength="20" 
                       pattern=".{8,20}" title="8 to 20 characters">
                
                <label>Password</label>
                <!-- Password Strength Bar -->
                <div class="strength-bar">
                    <div id="strength" class="strength"></div>
                </div>

                <input type="password" name="password" id="password" placeholder="Enter your password" required
                       minlength="8" maxlength="20" 
                       pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$"
                       title="8-20 characters, at least one letter, one number, and one special character">
                
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm your password" required>

                <button type="submit" name="register" class="login-button">Register</button>
            </div>
        </form>
        <div class="register-link">
            Already have an account? <a href="alogin.php">Login Here</a>
        </div>
    </div>

    <script>
        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strength');

        passwordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            let strength = 0;

            if (password.length >= 0 && 8) strength += 1; // Weak
            if (password.length >= 10 && /[A-Za-z]/.test(password) && /\d/.test(password)) strength += 1; // Medium
            if (password.length >= 12 && /[A-Za-z]/.test(password) && /\d/.test(password) && /[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 1; // Strong
            if (password.length >= 15 && /[A-Z]/.test(password) && /[a-z]/.test(password) && /\d/.test(password) && /[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 1; // Very Strong

            if (strength === 1) {
                strengthBar.style.width = "25%";
                strengthBar.className = 'strength weak';
            } else if (strength === 2) {
                strengthBar.style.width = "50%";
                strengthBar.className = 'strength medium';
            } else if (strength === 3) {
                strengthBar.style.width = "75%";
                strengthBar.className = 'strength pstrong';
            } else if (strength === 4) {
                strengthBar.style.width = "100%";
                strengthBar.className = 'strength strong';
            }
        });

        // Client-side form validation
        function validateForm() {
            const password = passwordInput.value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;

            if (password !== confirmPassword) {
                alert('Passwords do not match.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
