<?php
session_start();
require_once 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$task_id = filter_var($_POST['task_id'] ?? null, FILTER_VALIDATE_INT);

if (!$task_id) {
    $_SESSION['errors'][] = 'Invalid task ID.';
    header("Location: ../dashboard.php");
    exit;
}

try {
    // Delete the task
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$task_id, $user_id])) {
        $_SESSION['message'] = 'Task deleted successfully.';
        header("Location: ../dashboard.php");
        exit;
    } else {
        $_SESSION['errors'][] = 'Failed to delete task.';
        header("Location: ../dashboard.php");
        exit;
    }
} catch (Exception $e) {
    error_log('Error deleting task: ' . $e->getMessage());
    $_SESSION['errors'][] = 'An unexpected error occurred.';
    header("Location: ../dashboard.php");
    exit;
}
?>