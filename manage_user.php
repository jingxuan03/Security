<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sessionTimeout = 1800; 

// Check if last activity timestamp is set
if (isset($_SESSION['last_activity'])) {
    // Check if session has timed out
    if (time() - $_SESSION['last_activity'] > $sessionTimeout) {
        session_unset();
        session_destroy();
        header("Location: alogin.php"); // Redirect to login page
        exit;
    }
}
// Assuming you have a database connection in db_connection.php
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // Get the user_id from session

// Fetch all users from the database
$sql = "SELECT * FROM users"; // Adjust table name and columns if necessary
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC); // Fetch users into an associative array

// Handling delete user (if delete user button is clicked)
if (isset($_GET['delete'])) {
    $userId = $_GET['delete'];
    
    // Prepare SQL query to delete the user from the database
    $deleteSql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        // Redirect back to the users page after deletion
        header("Location: manage_user.php");  
        exit;
    } else {
        echo "Error deleting user: " . $stmt->error;
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="all_tasks2.css">
    <title>Manage Users</title>
</head>
<body>
    <div class="home">
        <aside class="asidebar">
            <?php include 'asidebar.php'; ?>
        </aside>

        <main class="content">
            <h1>Manage Users</h1>

            <!-- User List -->
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) === 0): ?>
                        <tr>
                            <td colspan="3">No users available</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <a href="manage_user.php?delete=<?php echo $user['id']; ?>" class="all-delete-button" onclick="return confirm('Do you wish to delete this user?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
