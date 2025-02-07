<?php
session_start();
require_once 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add or update a task
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'])) {
        // Update an existing task
        $id = filter_var($data['id'], FILTER_VALIDATE_INT);
        $title = filter_var($data['title'], FILTER_SANITIZE_STRING);
        $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
        $priority = filter_var($data['priority'], FILTER_SANITIZE_STRING);
        $due_date = filter_var($data['due_date'], FILTER_SANITIZE_STRING);
        $status = filter_var($data['status'], FILTER_SANITIZE_STRING);

        if (!$id || empty($title) || empty($description) || empty($priority) || empty($due_date) || empty($status)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        try {
            $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, priority = ?, due_date = ?, status = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$title, $description, $priority, $due_date, $status, $id, $user_id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
        }
    } else {
        // Add a new task
        $title = filter_var($data['title'], FILTER_SANITIZE_STRING);
        $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
        $priority = filter_var($data['priority'], FILTER_SANITIZE_STRING);
        $due_date = filter_var($data['due_date'], FILTER_SANITIZE_STRING);

        if (empty($title) || empty($description) || empty($priority) || empty($due_date)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        try {
            $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, priority, due_date, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$user_id, $title, $description, $priority, $due_date]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all tasks or a specific task
    if (isset($_GET['id'])) {
        // Fetch a specific task by ID
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid task ID.']);
            exit;
        }

        try {
            $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);
            $task = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($task) {
                echo json_encode(['success' => true, 'task' => $task]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Task not found.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
        }
    } else {
        // Fetch all tasks for the user
        try {
            $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'tasks' => $tasks]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Delete a task
    $data = json_decode(file_get_contents('php://input'), true);
    $id = filter_var($data['id'], FILTER_VALIDATE_INT);

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid task ID.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Task not found.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
    }
}
?>