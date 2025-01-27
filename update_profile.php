<?php
require_once 'db/db_config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = "";
$success = null;

// Check for success message
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']); // Clear the message after showing it
}

// Fetch the user's current profile data
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Update user profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $religion = $_POST['religion'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $date_of_birth = $_POST['date_of_birth'];
    $marital_status = $_POST['marital_status'];
    $height = $_POST['height'] ?? null;
    $weight = $_POST['weight'] ?? null;
    $education = $_POST['education'] ?? null;
    $work = $_POST['work'] ?? null;
    $about_yourself = $_POST['about_yourself'] ?? null;

    // Profile picture upload handling
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $allowed_file_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $file_type = $_FILES['profile_picture']['type'];
        if (!in_array($file_type, $allowed_file_types)) {
            $error = "Only JPG, JPEG, and PNG files are allowed.";
        } else {
            $profile_picture = time() . "_" . basename($_FILES['profile_picture']['name']);
            $upload_dir = "assets/images/";
            $upload_path = $upload_dir . $profile_picture;

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                if ($user['profile_picture'] && file_exists("assets/images/" . $user['profile_picture'])) {
                    unlink("assets/images/" . $user['profile_picture']); // Delete old picture
                }
                $user['profile_picture'] = $profile_picture;
            } else {
                $error = "Failed to upload profile picture.";
            }
        }
    }

    // Update database
    if (!$error) {
        $query = "UPDATE users SET name = ?, gender = ?, religion = ?, address = ?, contact_number = ?, date_of_birth = ?, marital_status = ?, profile_picture = ?, height = ?, weight = ?, education = ?, work = ?, about_yourself = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sssssssssssssi",
            $name,
            $gender,
            $religion,
            $address,
            $contact_number,
            $date_of_birth,
            $marital_status,
            $user['profile_picture'],
            $height,
            $weight,
            $education,
            $work,
            $about_yourself,
            $user_id
        );

        if ($stmt->execute()) {
            session_start();
            $_SESSION['success'] = "Profile updated successfully."; // Set the success message
            header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
            exit();
        } else {
            $error = "Error updating profile: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2>Update Profile</h2>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="Male" <?php echo ($user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="religion" class="form-label">Religion</label>
                <input type="text" class="form-control" id="religion" name="religion" value="<?php echo htmlspecialchars($user['religion']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="tel" class="form-control" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="date_of_birth" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo $user['date_of_birth']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="marital_status" class="form-label">Marital Status</label>
                <select class="form-control" id="marital_status" name="marital_status" required>
                    <option value="Single" <?php echo ($user['marital_status'] == 'Single') ? 'selected' : ''; ?>>Single</option>
                    <option value="Married" <?php echo ($user['marital_status'] == 'Married') ? 'selected' : ''; ?>>Married</option>
                    <option value="Divorced" <?php echo ($user['marital_status'] == 'Divorced') ? 'selected' : ''; ?>>Divorced</option>
                    <option value="Widowed" <?php echo ($user['marital_status'] == 'Widowed') ? 'selected' : ''; ?>>Widowed</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Profile Picture</label>
                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                <?php if ($user['profile_picture']): ?>
                    <img src="assets/images/<?php echo $user['profile_picture']; ?>" alt="Profile Picture" class="mt-2" width="150">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="height" class="form-label">Height (cm)</label>
                <input type="number" class="form-control" id="height" name="height" value="<?php echo $user['height']; ?>">
            </div>
            <div class="mb-3">
                <label for="weight" class="form-label">Weight (kg)</label>
                <input type="number" class="form-control" id="weight" name="weight" value="<?php echo $user['weight']; ?>">
            </div>
            <div class="mb-3">
                <label for="education" class="form-label">Education</label>
                <input type="text" class="form-control" id="education" name="education" value="<?php echo htmlspecialchars($user['education']); ?>">
            </div>
            <div class="mb-3">
                <label for="work" class="form-label">Work</label>
                <input type="text" class="form-control" id="work" name="work" value="<?php echo htmlspecialchars($user['work']); ?>">
            </div>
            <div class="mb-3">
                <label for="about_yourself" class="form-label">About Yourself</label>
                <textarea class="form-control" id="about_yourself" name="about_yourself"><?php echo htmlspecialchars($user['about_yourself']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>