<?php
session_start();
require_once 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle form submission to add a new task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $priority = filter_var($_POST['priority'], FILTER_SANITIZE_STRING);
    $due_date = filter_var($_POST['due_date'], FILTER_SANITIZE_STRING);

    try {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, priority, due_date, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $title, $description, $priority, $due_date]);
        header("Location: ../dashboard.php"); // Redirect to dashboard.php
        exit;
    } catch (Exception $e) {
        echo "An unexpected error occurred: " . htmlspecialchars($e->getMessage());
        exit;
    }
}

// Fetch all tasks for the user
try {
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "An unexpected error occurred: " . htmlspecialchars($e->getMessage());
    exit;
}
?>
<!-- Render Tasks Dynamically -->
<?php foreach ($tasks as $task): ?>
    <div class="task-card">
        <h3><?php echo htmlspecialchars($task['title']); ?></h3>
        <p><?php echo htmlspecialchars($task['description']); ?></p>
        <p><strong>Priority:</strong> <?php echo htmlspecialchars($task['priority']); ?></p>
        <p><strong>Due Date:</strong> <?php echo htmlspecialchars($task['due_date']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($task['status']); ?></p>
        <a href="task-details.php?id=<?php echo $task['id']; ?>" class="btn primary">View Details</a>
    </div>
<?php endforeach; ?>