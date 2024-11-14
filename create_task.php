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
// Include the database connection
include 'db_connection.php';

// Initialize variables
$taskName = '';
$priority = 'Low';
$status = 'Pending';
$dueDate = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $taskName = $_POST['taskName'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $dueDate = $_POST['dueDate'];

    // Check if required fields are filled
    if ($taskName && $dueDate) {
        // Get the user_id from the session (assuming the user is logged in)
        $userId = $_SESSION['user_id'];  // Assuming user_id is stored in session

        // Insert the task into the database, including the user_id
        $sql = "INSERT INTO tasks (task_name, priority, status, due_date, user_id) 
        VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $taskName, $priority, $status, $dueDate, $userId);
        
        if ($stmt->execute()) {
            // Show success alert
            echo "<script>alert('Task created successfully!');</script>";

            // Redirect to All Tasks page
            header('Location: all_tasks.php');
            exit;
        } else {
            echo "<script>alert('Error creating task. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Task Name and Due Date are required!');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="create_task.css">
    <title>Create Task</title>
</head>
<body>
<div class="create-form">
        <button type="button" class="back-button" onclick="window.location.href='all_tasks.php'">Back</button>
        <h2>Create Task</h2>
        
        <form method="POST" class="task-form">
            <input 
                type="text" 
                name="taskName" 
                placeholder="Task Name" 
                value="<?php echo htmlspecialchars($taskName); ?>"
                required 
            />
            
            <select name="priority" require>
                    <option value="Low" <?php echo $priority === 'Low' ? 'selected' : ''; ?>>Low</option>
                    <option value="Medium" <?php echo $priority === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="High" <?php echo $priority === 'High' ? 'selected' : ''; ?>>High</option>
            </select>
            
            <select name="status" require>
                    <option value="Pending" <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="In Progress" <?php echo $status === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="Completed" <?php echo $status === 'Completed' ? 'selected' : ''; ?>>Completed</option>
            </select>
            
            <input
                    type="date"
                    name="dueDate"
                    value="<?php echo htmlspecialchars($dueDate); ?>"
                    required
            />
            
            <button type="submit" class="submit-button">Create</button>
        </form>
    </div>
</body>
</html>
