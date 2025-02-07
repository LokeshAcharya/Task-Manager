<?php
session_start();
require_once 'backend/db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Task Manager</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="navbar">
        <div class="logo">
            <img src="assets/images/logo.png" alt="Task Manager Logo">
            <h1>Task Manager</h1>
        </div>
        <nav>
            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <form action="backend/logout.php" method="POST" style="display: inline;">
                <button type="submit" class="btn secondary"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </header>
    <main>
        <!-- Add Task Section -->
        <section class="task-form">
            <h2><i class="fas fa-plus-circle"></i> Add New Task</h2>
            <form action="backend/dashboard.php" method="POST">
                <input type="text" id="task-title" name="title" placeholder="Task Title" required>
                <textarea id="task-desc" name="description" placeholder="Task Description" required></textarea>
                <select id="task-priority" name="priority">
                    <option value="Low">Low Priority</option>
                    <option value="Medium">Medium Priority</option>
                    <option value="High">High Priority</option>
                </select>
                <input type="date" id="task-due-date" name="due_date" required>
                <button type="submit" class="btn primary"><i class="fas fa-check"></i> Add Task</button>
            </form>
        </section>

        <!-- Task Filters -->
        <section class="task-filters">
            <input type="text" id="task-search" placeholder="Search tasks..." oninput="filterTasks()">
            <select id="task-filter-priority" onchange="filterTasks()">
                <option value="all">All Priorities</option>
                <option value="Low">Low Priority</option>
                <option value="Medium">Medium Priority</option>
                <option value="High">High Priority</option>
            </select>
        </section>

        <!-- Task List -->
        <section id="task-list" class="task-list">
            <?php
            try {
                $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($tasks) === 0) {
                    echo '<p>No tasks found. Add a new task!</p>';
                } else {
                    foreach ($tasks as $task) {
                        echo '<div class="task-card">';
                        echo '<h3>' . htmlspecialchars($task['title']) . '</h3>';
                        echo '<p>' . htmlspecialchars($task['description']) . '</p>';
                        echo '<p><strong>Priority:</strong> ' . htmlspecialchars($task['priority']) . '</p>';
                        echo '<p><strong>Due Date:</strong> ' . htmlspecialchars($task['due_date']) . '</p>';
                        echo '<p><strong>Status:</strong> ' . htmlspecialchars($task['status']) . '</p>';
                        echo '<a href="backend/task-details.php?id=' . $task['id'] . '" class="btn primary">View Details</a>';
                        echo '</div>';
                    }
                }
            } catch (Exception $e) {
                echo '<p>An unexpected error occurred while fetching tasks.</p>';
            }
            ?>
        </section>

        <!-- Progress Bar -->
        <div class="progress-bar-container">
            <p>Completion Progress:</p>
            <div class="progress-bar">
                <div id="progress-bar-fill"></div>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Task Manager. All rights reserved.</p>
    </footer>
    <script>
        // JavaScript for Filtering Tasks
        function filterTasks() {
            const searchQuery = document.getElementById('task-search').value.toLowerCase();
            const priorityFilter = document.getElementById('task-filter-priority').value;
            Array.from(document.querySelectorAll('.task-card')).forEach(taskCard => {
                const title = taskCard.querySelector('h3').innerText.toLowerCase();
                const priority = taskCard.querySelector('p strong + span').innerText;
                const matchesSearch = title.includes(searchQuery);
                const matchesPriority = priorityFilter === 'all' || priority === priorityFilter;
                taskCard.style.display = matchesSearch && matchesPriority ? 'block' : 'none';
            });
        }
    </script>
</body>
</html>