<?php
session_start();
require_once 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the JSON payload
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data === null || !is_array($data)) {
        echo json_encode(['success' => false, 'message' => 'Invalid or missing JSON data.']);
        exit;
    }

    // Extract and validate inputs
    $full_name = filter_var($data['full_name'] ?? '', FILTER_SANITIZE_STRING);
    $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $data['password'] ?? '';

    if (empty($full_name) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Full name and email are required.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit;
    }

    try {
        // Update the user's profile in the database
        $sql = "UPDATE users SET username = ?, email = ?";
        $params = [$full_name, $email];

        if (!empty($password)) {
            if (strlen($password) < 6) {
                echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
                exit;
            }
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $sql .= ", password_hash = ?";
            $params[] = $password_hash;
        }

        $sql .= " WHERE id = ?";
        $params[] = $user_id;

        $stmt = $conn->prepare($sql);
        if ($stmt->execute($params)) {
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update profile.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
    }
}
?>