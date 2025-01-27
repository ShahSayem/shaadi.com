<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>shaadi.com</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* Style for the background image */
        .bg-img {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
            opacity: 0.7;
        }

        /* Style for text overlay */
        .overlay-content {
            position: relative;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            margin-top: 20%;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- Background Image -->
    <img src="assets/images/bg.jpg" class="bg-img" alt="Background Image">

    <!-- Overlay Content -->
    <div class="container text-center overlay-content">
        <h1>Welcome to shaadi.com</h1>
        <p class="lead">Find your perfect life partner!!!</p>

        <?php if (!$is_logged_in): ?>
            <a href="register.php" class="btn btn-primary">Register</a>
            <a href="login.php" class="btn btn-primary">Login</a>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center text-lg-start mt-5">
        <div class="text-center p-3">
            &copy; 2025 shaadi.com || All Rights Reserved || Developed with ❤️ by <a href="https://www.linkedin.com/in/shah-sayem/" target="_blank" rel="noopener noreferrer">Shah Sayem Ahmad</a>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
