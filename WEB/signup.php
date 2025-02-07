<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Task Manager</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/auth.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Authentication Container -->
    <div class="auth-container">
        <div class="auth-box">
            <!-- Header -->
            <h2>Create an Account</h2>
            <!-- Sign Up Form -->
            <form action="backend/register.php" method="POST">
                <!-- Username Field -->
                <div class="input-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Enter your username" 
                        required 
                    >
                </div>
                <!-- Email Field -->
                <div class="input-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="Enter your email" 
                        required
                    >
                </div>
                <!-- Password Field -->
                <div class="input-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password" 
                        required 
                    >
                </div>
                <!-- Submit Button -->
                <button type="submit" class="btn primary">Sign Up</button>
            </form>
            <!-- Login Link -->
            <p>Already have an account? <a href="login.php">Log In</a></p>
        </div>
    </div>
</body>
</html>