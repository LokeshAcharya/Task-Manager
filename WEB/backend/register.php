<?php
session_start();
require_once 'db.php';

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../signup.php"); // Redirect back to signup page if not a POST request
    exit;
}

// Retrieve and sanitize inputs
$username = filter_var($_POST['username'] ?? '', FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';

// Validate inputs
if (empty($username) || empty($email) || empty($password)) {
    $_SESSION['error'] = 'All fields are required.';
    header("Location: ../signup.php");
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Invalid email address.';
    header("Location: ../signup.php");
    exit;
}
if (strlen($password) < 6) {
    $_SESSION['error'] = 'Password must be at least 6 characters long.';
    header("Location: ../signup.php");
    exit;
}

// Hash the password
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// Insert the new user into the database
try {
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $email, $password_hash])) {
        // Redirect to login page on success
        header("Location: ../login.php");
        exit;
    } else {
        $_SESSION['error'] = 'Failed to register user.';
        header("Location: ../signup.php");
        exit;
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'An unexpected error occurred.';
    header("Location: ../signup.php");
    exit;
}
?>