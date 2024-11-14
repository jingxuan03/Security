<?php
// Start the session and include database connection
session_start();
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

// Define today’s date
$today = date("Y-m-d");

// Filter tasks into different categories
if ($tasks && is_array($tasks)) {
    // Filter ongoing tasks
    $incompleteTasks = array_filter($tasks, function($task) {
        return $task['status'] !== 'Completed';
    });

    // Filter completed tasks
    $completedTasks = array_filter($tasks, function($task) {
        return $task['status'] === 'Completed';
    });

    // Filter overdue tasks
    $overdueTasks = array_filter($tasks, function($task) use ($today) {
        return $task['due_date'] < $today && $task['status'] !== 'Completed';
    });
} else {
    // If no tasks exist, initialize empty arrays
    $incompleteTasks = $completedTasks = $overdueTasks = [];
}

// Helper functions for CSS classes (priority and status)
function getPriorityClass($priority) {
    switch ($priority) {
        case 'Low':
            return 'priority-low'; // Green
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
            return 'status-low'; // Green
        case 'In Progress':
            return 'status-medium'; // Yellow
        case 'Pending':
            return 'status-high'; // Red
        default:
            return '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="home.css">
    <title>Dashboard</title>
</head>
<body>
    <div class="home">
        <?php include 'sidebar.php'; ?>

        <main class="content">
            <h1>Dashboard</h1>
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>

            <!-- Ongoing Tasks -->
            <table>
                <thead>
                    <tr>
                        <th colspan="4" class="ongoing-header">Ongoing Tasks</th>
                    </tr>
                    <tr>
                        <th>Task Name</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($incompleteTasks)): ?>
                        <tr>
                            <td colspan="4">No ongoing tasks available</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($incompleteTasks as $task): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                                <td class="<?php echo getPriorityClass($task['priority']); ?>">
                                    <?php echo htmlspecialchars($task['priority']); ?>
                                </td>
                                <td class="<?php echo getStatusClass($task['status']); ?>">
                                    <?php echo htmlspecialchars($task['status']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($task['due_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Overdue Tasks -->
            <table>
                <thead>
                    <tr>
                        <th colspan="4" class="overdue-header">Overdue Tasks</th>
                    </tr>
                    <tr>
                        <th>Task Name</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($overdueTasks)): ?>
                        <tr>
                            <td colspan="4">No overdue tasks available</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($overdueTasks as $task): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                                <td class="<?php echo getPriorityClass($task['priority']); ?>">
                                    <?php echo htmlspecialchars($task['priority']); ?>
                                </td>
                                <td class="<?php echo getStatusClass($task['status']); ?>">
                                    <?php echo htmlspecialchars($task['status']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($task['due_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Completed Tasks -->
            <table>
                <thead>
                    <tr>
                        <th colspan="4" class="completed-header">Completed Tasks</th>
                    </tr>
                    <tr>
                        <th>Task Name</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($completedTasks)): ?>
                        <tr>
                            <td colspan="4">No completed tasks available</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($completedTasks as $task): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                                <td class="<?php echo getPriorityClass($task['priority']); ?>">
                                    <?php echo htmlspecialchars($task['priority']); ?>
                                </td>
                                <td class="<?php echo getStatusClass($task['status']); ?>">
                                    <?php echo htmlspecialchars($task['status']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($task['due_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        </main>
    </div>
</body>
</html>
