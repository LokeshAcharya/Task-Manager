<?php
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection for fetching profile data
require_once 'backend/db.php';

$user_id = $_SESSION['user_id'];

try {
    // Fetch user details from the database
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo '<p>Profile not found. Please contact support.</p>';
        exit;
    }
} catch (Exception $e) {
    error_log('Error fetching profile data: ' . $e->getMessage());
    echo '<p>An unexpected error occurred while fetching profile data. Please try again later.</p>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Task Manager</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="logo">
            <img src="assets/images/logo.png" alt="Task Manager Logo">
            <h1>Task Manager</h1>
        </div>
        <nav>
            <a href="dashboard.php"><i class="fas fa-tasks"></i> Dashboard</a>
            <form action="backend/logout.php" method="POST" style="display: inline;">
                <button type="submit" class="btn secondary"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>
    </header>

    <!-- Profile Section -->
    <main>
        <section class="auth-container">
            <div class="auth-box">
                <h2><i class="fas fa-user"></i> Profile</h2>

                <!-- Display Success/Error Messages -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="success-message">
                        <p><?php echo htmlspecialchars($_SESSION['message']); ?></p>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['errors'])): ?>
                    <div class="error-messages">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>

                <!-- Profile Form -->
                <form id="profile-form">
                    <div class="input-group">
                        <label for="username"><i class="fas fa-user"></i> Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter your username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="password"><i class="fas fa-lock"></i> New Password (Optional)</label>
                        <input type="password" id="password" name="password" placeholder="Enter a new password">
                    </div>
                    <button type="submit" class="btn primary">Update Profile</button>
                </form>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Task Manager. All rights reserved.</p>
    </footer>

    <!-- JavaScript for Handling Profile Updates -->
    <script>
        document.getElementById('profile-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            // Validate inputs
            if (!username || !email) {
                alert('Username and email are required.');
                return;
            }

            try {
                const response = await fetch('backend/profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, email, password })
                });

                const result = await response.json();
                if (result.success) {
                    alert(result.message);
                    window.location.reload(); // Reload the page to reflect changes
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
            }
        });
    </script>
</body>
</html>