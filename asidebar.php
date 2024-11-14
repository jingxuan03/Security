<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']); // Get the current page
$isOpen = true; // Default sidebar state
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Sidebar.css">
    <title>Task Master - Admin Sidebar</title>
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('closed');
        }
    </script>
</head>
<body>
    <aside class="sidebar <?= $isOpen ? 'open' : 'closed' ?>">
        <button class="toggle-btn" onclick="toggleSidebar()">|||</button>

        <?php if ($isOpen): ?>
        <div class="sidebar-content">
            <h1>Task Master</h1>
            <nav>
                <ul>
                    <li class="<?= $current_page == 'admin.php' ? 'active' : '' ?>">
                        <a href="admin.php">Dashboard</a>
                    </li>
                    <li class="<?= $current_page == 'manage_user.php' ? 'active' : '' ?>">
                        <a href="manage_user.php">Manage User</a>
                    </li>
                </ul>
            </nav>
            <form action="logout.php" method="post">
                <button type="submit" class="logout">Logout</button>
            </form>
        </div>
        <?php endif; ?>
    </aside>
</body>
</html>
