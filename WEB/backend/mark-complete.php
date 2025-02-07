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
    // Update the task status to "Complete"
    $stmt = $conn->prepare("UPDATE tasks SET status = 'Complete' WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$task_id, $user_id])) {
        $_SESSION['message'] = 'Task marked as complete.';
        header("Location: ../dashboard.php");
        exit;
    } else {
        $_SESSION['errors'][] = 'Failed to mark task as complete.';
        header("Location: ../dashboard.php");
        exit;
    }
} catch (Exception $e) {
    error_log('Error marking task as complete: ' . $e->getMessage());
    $_SESSION['errors'][] = 'An unexpected error occurred.';
    header("Location: ../dashboard.php");
    exit;
}
?>