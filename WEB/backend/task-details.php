<?php
session_start();
require_once '../backend/db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$task_id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$task_id) {
    echo '<p>Invalid task ID.</p>';
    exit;
}

try {
    // Fetch the task details from the database
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $user_id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        echo '<p>Task not found or you do not have permission to view it.</p>';
        exit;
    }
} catch (Exception $e) {
    echo '<p>An unexpected error occurred. Please try again later.</p>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Details - Task Manager</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="logo">
            <img src="../assets/images/logo.png" alt="Task Manager Logo">
            <h1>Task Manager</h1>
        </div>
        <nav>
            <a href="/WEB/dashboard.php"><i class="fas fa-tasks"></i> Back to Dashboard</a>
            <form action="../backend/logout.php" method="POST" style="display: inline;">
                <button type="submit" class="btn secondary"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <section id="task-detail-container" class="task-detail">
            <h2><?php echo htmlspecialchars($task['title']); ?></h2>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($task['description']); ?></p>
            <p><strong>Priority:</strong> <?php echo htmlspecialchars($task['priority']); ?></p>
            <p><strong>Due Date:</strong> <?php echo htmlspecialchars($task['due_date']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($task['status']); ?></p>

            <!-- Buttons for Mark as Complete and Delete -->
            <div class="task-actions">
                <!-- Mark as Complete Form -->
                <form action="../backend/mark-complete.php" method="POST" style="display: inline;">
                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                    <button type="submit" class="btn success">Mark as Complete</button>
                </form>

                <!-- Delete Task Form -->
                <form action="../backend/delete-task.php" method="POST" style="display: inline;">
                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                    <button type="submit" class="btn danger">Delete Task</button>
                </form>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Task Manager. All rights reserved.</p>
    </footer>
</body>
</html>