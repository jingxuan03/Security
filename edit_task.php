<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Include database connection
require_once 'db_connection.php'; // Make sure you have a file that connects to your database

// Get the task ID from URL
$taskId = isset($_GET['id']) ? $_GET['id'] : null;
if ($taskId) {
    // Fetch the task details from the database
    $sql = "SELECT * FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $taskId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
    } else {
        echo "Task not found.";
        exit;
    }
}

// Handle form submission to update the task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskName = $_POST['taskName'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $dueDate = $_POST['dueDate'];

    // Update the task in the database
    $sql = "UPDATE tasks SET task_name = ?, priority = ?, status = ?, due_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $taskName, $priority, $status, $dueDate, $taskId);
    
    if ($stmt->execute()) {
        echo "<script>alert('Task updated successfully!'); window.location.href = 'all_tasks.php';</script>";
    } else {
        echo "Error updating task: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="create_task.css">
</head>
<body>
    <div class="edit-form">
        <button type="button" class="edit-back-button" onclick="window.location.href='all_tasks.php'">Back</button>
        <h2>Edit Task</h2>
        
        <form action="edit_task.php?id=<?php echo $taskId; ?>" method="POST" class="edit-task-form">
            <input 
                type="text" 
                name="taskName" 
                placeholder="Task Name" 
                value="<?php echo htmlspecialchars($task['task_name']); ?>" 
                required 
            />
            
            <select name="priority" required>
                <option value="Low" <?php echo $task['priority'] === 'Low' ? 'selected' : ''; ?>>Low</option>
                <option value="Medium" <?php echo $task['priority'] === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                <option value="High" <?php echo $task['priority'] === 'High' ? 'selected' : ''; ?>>High</option>
            </select>
            
            <select name="status" required>
                <option value="Pending" <?php echo $task['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="In Progress" <?php echo $task['status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="Completed" <?php echo $task['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
            </select>
            
            <input 
                type="date" 
                name="dueDate" 
                value="<?php echo $task['due_date']; ?>" 
                required 
            />
            
            <button type="submit" class="confirm-button">Confirm</button>
        </form>
    </div>
</body>
</html>
