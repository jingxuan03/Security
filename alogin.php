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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users_admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user['id'];  // Store user_id in the session
        
        // Remember Me functionality
        if (isset($_POST['rememberMe'])) {
            setcookie("username", $username, time() + (86400 * 30), "/"); // 30 days expiration
        }

        echo "<script>alert('Login successful!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Invalid username or password.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Admin Login</title>
</head>
<body>
    <div class="login-container">           
        <a href="gateway.php" class="back-button">Back</a>
        <h2>Admin Login</h2>
        <form method="POST" action="alogin.php">
            <div class="login-form-group">
                <label>Username</label>
                <div class="username-input-container">
                    <input type="text" name="username" placeholder="Enter your username" required>
                </div>

                <label>Password</label>
                <div class="password-input-container">
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>

                <div class="register-link">
                    <a href="forgot_password.php">Forgot Password?</a>
                </div>

                <button type="submit" name="login" class="login-button">Login</button>
            </div>
        </form>
        <div class="register-link">
            Don't have an account? <a href="aregister.php">Register Here</a>
        </div>
    </div>
</body>
</html>
