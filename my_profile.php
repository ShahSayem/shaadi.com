<?php
require_once 'db/db_config.php';
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .profile-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .profile-info ul {
            list-style: none;
            padding: 0;
        }

        .profile-info ul li {
            margin-bottom: 10px;
        }

        .profile-info ul li strong {
            display: inline-block;
            width: 120px;
            font-weight: bold;
            color: #333;
        }

        .action-buttons a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="profile-card">
                    <div class="profile-header">
                        <img src="assets/images/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
                        <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                    </div>
                    <div class="profile-info">
                        <ul>
                            <li><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
                            <li><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></li>
                            <li><strong>Religion:</strong> <?php echo htmlspecialchars($user['religion']); ?></li>
                            <li><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></li>
                            <li><strong>Contact Number:</strong> <?php echo htmlspecialchars($user['contact_number']); ?></li>
                            <li><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['date_of_birth']); ?></li>
                            <li><strong>Marital Status:</strong> <?php echo htmlspecialchars($user['marital_status']); ?></li>
                            <?php if (!empty($user['height'])): ?>
                                <li><strong>Height:</strong> <?php echo htmlspecialchars($user['height']); ?> cm</li>
                            <?php endif; ?>
                            <?php if (!empty($user['weight'])): ?>
                                <li><strong>Weight:</strong> <?php echo htmlspecialchars($user['weight']); ?> kg</li>
                            <?php endif; ?>
                            <?php if (!empty($user['education'])): ?>
                                <li><strong>Education:</strong> <?php echo htmlspecialchars($user['education']); ?></li>
                            <?php endif; ?>
                            <?php if (!empty($user['work'])): ?>
                                <li><strong>Work:</strong> <?php echo htmlspecialchars($user['work']); ?></li>
                            <?php endif; ?>
                            <?php if (!empty($user['created_at'])): ?>
                                <li><strong>Joined:</strong> <?php echo date("F d, Y", strtotime($user['created_at'])); ?></li>
                            <?php endif; ?>
                            <?php if (!empty($user['about_yourself'])): ?>
                                <li><strong>About:</strong> <?php echo htmlspecialchars($user['about_yourself']); ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="action-buttons mt-4 text-center">
                        <a href="update_profile.php" class="btn btn-primary">Update Profile</a>
                        <a href="search.php" class="btn btn-secondary">Back to Profiles</a>
                        <a href="logout.php" class="btn btn-danger">Logout</a>
                        <a href="delete_profile.php" class="btn btn-danger">Delete Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
