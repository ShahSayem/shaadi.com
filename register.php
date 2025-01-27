<?php
require_once 'db/db_config.php';

// Initialize variables
$success = $error = "";
$allowed_file_types = ['image/jpeg', 'image/png', 'image/jpg'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error_messages = [];

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gender = $_POST['gender'];
    $religion = $_POST['religion'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $date_of_birth = $_POST['date_of_birth'];
    $marital_status = $_POST['marital_status'];
    // $profile_picture = $_FILES['profile_picture']['name'] ?? null;
    $height = $_POST['height'] ?? null;
    $weight = $_POST['weight'] ?? null;
    $education = $_POST['education'] ?? null;
    $work = $_POST['work'] ?? null;
    $about_yourself = $_POST['about_yourself'] ?? null;


    // Validate required fields
    if (empty($name)) 
        $error_messages[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $error_messages[] = "Invalid email format.";
    if ($password !== $confirm_password) 
        $error_messages[] = "Passwords do not match.";
    if (empty($gender)) 
        $error_messages[] = "Gender is required.";
    if (empty($religion)) 
        $error_messages[] = "Religion is required.";
    if (empty($address)) 
        $error_messages[] = "Address is required.";
    if (empty($contact_number)) 
        $error_messages[] = "Contact number is required.";
    if (empty($date_of_birth)) 
        $error_messages[] = "Date of birth is required.";
    if (empty($marital_status)) 
        $error_messages[] = "Marital status is required.";


    // Profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file_tmp_path = $_FILES['profile_picture']['tmp_name'];
        $file_name = $_FILES['profile_picture']['name'];
        $file_size = $_FILES['profile_picture']['size'];
        $file_type = $_FILES['profile_picture']['type'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validate file type
        if (in_array($file_type, $allowed_file_types)) {
            // $upload_dir = 'assets/images/';
            // $dest_path = $upload_dir . $file_name;
            $dest_path = $file_name;

            if (move_uploaded_file($file_tmp_path, $dest_path)) {
                $profile_picture = $dest_path; // Set the path for database insertion
                // $success = "File uploaded successfully.";
            } else {
                $error = "There was an error moving the uploaded file.";
            }
        } else {
            $error = "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
        }
    } else {
        $error = "Profile picture is required.";
    }

    // Insert user into the database
    if (!$error) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (name, email, password, gender, religion, address, contact_number, date_of_birth, marital_status, profile_picture, height, weight, education, work, about_yourself) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sssssssssssssss",
            $name,
            $email,
            $password_hash,
            $gender,
            $religion,
            $address,
            $contact_number,
            $date_of_birth,
            $marital_status,
            $profile_picture,
            $height,
            $weight,
            $education,
            $work,
            $about_yourself
        );

        if ($stmt->execute()) {
            $success = "Registration successful! You can now log in.";
        } else {
            $error = "Error: " . $stmt->error;
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
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2>Register</h2>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form id="registerForm" action="register.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password must be at least 6 characters long and include at least one letter and one number" oninput="validatePassword()" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" oninput="validatePassword()" required>
                <span id="password-message"></span>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="">Select</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="religion" class="form-label">Religion</label>
                <input type="text" class="form-control" id="religion" name="religion" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="mb-3">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="tel" class="form-control" id="contact_number" name="contact_number" required>
            </div>
            <div class="mb-3">
                <label for="date_of_birth" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
            </div>
            <div class="mb-3">
                <label for="marital_status" class="form-label">Marital Status</label>
                <select class="form-control" id="marital_status" name="marital_status" required>
                    <option value="">Select Status</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Divorced">Divorced</option>
                    <option value="Widowed">Widowed</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Profile Picture</label>
                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="assets/images/*" required>
            </div>
            <div class="mb-3">
                <label for="height" class="form-label">Height (cm)</label>
                <input type="number" class="form-control" id="height" name="height">
            </div>
            <div class="mb-3">
                <label for="weight" class="form-label">Weight (kg)</label>
                <input type="number" class="form-control" id="weight" name="weight">
            </div>
            <div class="mb-3">
                <label for="education" class="form-label">Education</label>
                <input type="text" class="form-control" id="education" name="education">
            </div>
            <div class="mb-3">
                <label for="work" class="form-label">Work</label>
                <input type="text" class="form-control" id="work" name="work">
            </div>
            <div class="mb-3">
                <label for="about_yourself" class="form-label">About Yourself</label>
                <textarea class="form-control" id="about_yourself" name="about_yourself"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
    <script src="reg_validation.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
