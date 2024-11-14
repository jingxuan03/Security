<?php
// Database connection setup
$host = 'localhost';
$db = 'task_master';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verify if the token exists and has not expired
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // Update the password and clear the token
            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
            $stmt->bind_param("si", $new_password, $user['id']);
            $stmt->execute();

            echo "<script>alert('Password has been reset successfully!'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid or expired token.'); window.location.href='login.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Reset Password</title>
</head>
<body>
    <div class="login-container">
        <h2>Reset Password</h2>
        <form method="POST">
            <div class="username-input-container">
                <label>New Password</label>
                <input type="password" name="password" placeholder="Enter new password" required>
                <button type="submit" class="reset-button">Reset Password</button>
            </div>
        </form>
    </div>
</body>
</html>
