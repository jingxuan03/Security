<?php
// Include session and database connection
session_start();
include 'db_connection.php'; // Your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // Get user_id from session

// Fetch the current user details
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get updated data from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password if it's not empty
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $updateSql = "UPDATE users SET username = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssi", $username, $password, $user_id);
    } else {
        $updateSql = "UPDATE users SET username = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("si", $username, $user_id);
    }

    if ($stmt->execute()) {
        // Success: Redirect to another page or show success message
        $_SESSION['username'] = $username; // Update session username
        header('Location: home.php');
        exit;
    } else {
        // Error handling
        $error = "There was an error updating your information.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="settings.css">
</head>
<body>
    <div class="settings-container">
    <?php include 'sidebar.php'; ?>
        <h2>Settings</h2>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Leave empty if not changing">
                <button type="submit" class="submit-button">Save Changes</button>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            </div>
        </form>
    </div>
</body>
</html>
