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
        header("Location: login.php"); // Redirect to login page
        exit;
    }
}
// Update last activity timestamp
$_SESSION['last_activity'] = time();

// Assuming you have a database connection in db_connection.php
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // Get the user_id from session

// Fetch the username from the database using the user_id
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind the user_id (integer) to the query
$stmt->execute();
$result = $stmt->get_result();

// Check if a user was found
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc(); // Fetch the user data
    $username = $user['username']; // Get the username from the result
} else {
    // Handle error if no user is found
    $username = "Guest";
}

// Fetch tasks from the database using user_id
$sql = "SELECT * FROM tasks WHERE user_id = ?"; // Filter tasks by user_id
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind the user_id (integer) to the query
$stmt->execute();
$result = $stmt->get_result();
$tasks = $result->fetch_all(MYSQLI_ASSOC); // Fetch tasks into an associative array

if ($result->num_rows > 0) {
    // Store tasks in an array
    while($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}

function getPriorityClass($priority) {
    switch ($priority) {
        case 'Low':
            return 'priority-low';  // Green
        case 'Medium':
            return 'priority-medium'; // Yellow
        case 'High':
            return 'priority-high'; // Red
        default:
            return '';
    }
}

function getStatusClass($status) {
    switch ($status) {
        case 'Completed':
            return 'status-low';  // Green
        case 'In Progress':
            return 'status-medium'; // Yellow
        case 'Pending':
            return 'status-high'; // Red
        default:
            return '';
    }
}

// Handling delete task (if delete task button is clicked)
if (isset($_GET['delete'])) {
    $taskId = $_GET['delete'];
    
    // Prepare SQL query to delete the task from the database
    $deleteSql = "DELETE FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $taskId);
    
    if ($stmt->execute()) {
        // Redirect back to the tasks page after deletion
        header("Location: admin.php");  
        exit;
    } else {
        echo "Error deleting task: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="all_tasks.css">
    <title>All Tasks</title>
</head>
<body>
    <div class="alltasks">
        <?php include 'sidebar.php'; ?>
        
        <main class="content">
            <h1>All Tasks</h1>
            <nav>
                <ul>
                    <div class="create-button">              
                        <li><a href="create_task.php">+ Create</a></li>
                    </div>
                </ul>
            </nav>
            
            <table>
                <thead>
                    <tr>
                        <th>Task Name</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($tasks) === 0): ?>
                        <tr>
                            <td colspan="5">No tasks available</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                                <td class="<?php echo getPriorityClass($task['priority']); ?>">
                                    <?php echo htmlspecialchars($task['priority']); ?>
                                </td>
                                <td class="<?php echo getStatusClass($task['status']); ?>">
                                    <?php echo htmlspecialchars($task['status']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($task['due_date']); ?></td>
                                <td>
                                    <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="all-edit-button">Edit</a>
                                    <a href="all_tasks.php?delete=<?php echo $task['id']; ?>" class="all-delete-button" onclick="return confirm('Do you wish to delete this task?')">Delete</a>
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
