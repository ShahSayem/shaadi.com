<?php
// Include database configuration
require_once 'db/db_config.php';

// Start the session
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$curr_id = (int) $_SESSION['user_id'];

// Handle search filters
$name = isset($_GET['name']) ? trim($_GET['name']) : '';
$gender = isset($_GET['gender']) ? trim($_GET['gender']) : '';
$religion = isset($_GET['religion']) ? trim($_GET['religion']) : '';
$marital_status = isset($_GET['marital_status']) ? trim($_GET['marital_status']) : '';
$address = isset($_GET['address']) ? trim($_GET['address']) : '';
$work = isset($_GET['work']) ? trim($_GET['work']) : '';

// Construct the query dynamically
$query = "SELECT id, name, email, address, contact_number, profile_picture, created_at FROM users WHERE 1=1";
$params = [];
$types = '';

if ($name) {
    $query .= " AND name LIKE ?";
    $params[] = "%$name%";
    $types .= 's';
}
if ($gender) {
    $query .= " AND gender = ?";
    $params[] = $gender;
    $types .= 's';
}
if ($religion) {
    $query .= " AND religion LIKE ?";
    $params[] = "%$religion%";
    $types .= 's';
}
if ($marital_status) {
    $query .= " AND marital_status = ?";
    $params[] = $marital_status;
    $types .= 's';
}
if ($address) {
    $query .= " AND address LIKE ?";
    $params[] = "%$address%";
    $types .= 's';
}
if ($work) {
    $query .= " AND work LIKE ?";
    $params[] = "%$work%";
    $types .= 's';
}

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$profiles = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Profiles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .profile-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Search Profiles</h2>
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">
            </div>
            <div class="col-md-3">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-select" id="gender" name="gender">
                    <option value="" <?php if (!$gender) echo 'selected'; ?>>Select Gender</option>
                    <option value="Male" <?php if ($gender === 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($gender === 'Female') echo 'selected'; ?>>Female</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="religion" class="form-label">Religion</label>
                <input type="text" class="form-control" id="religion" name="religion" value="<?php echo htmlspecialchars($religion); ?>">
            </div>
            <div class="col-md-3">
                <label for="marital_status" class="form-label">Marital Status</label>
                <select class="form-select" id="marital_status" name="marital_status">
                    <option value="" <?php if (!$marital_status) echo 'selected'; ?>>Select Status</option>
                    <option value="Single" <?php if ($marital_status === 'Single') echo 'selected'; ?>>Single</option>
                    <option value="Married" <?php if ($marital_status === 'Married') echo 'selected'; ?>>Married</option>
                    <option value="Divorced" <?php if ($marital_status === 'Divorced') echo 'selected'; ?>>Divorced</option>
                    <option value="Widowed" <?php if ($marital_status === 'Widowed') echo 'selected'; ?>>Widowed</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>">
            </div>
            <div class="col-md-3">
                <label for="work" class="form-label">Work</label>
                <input type="text" class="form-control" id="work" name="work" value="<?php echo htmlspecialchars($work); ?>">
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <div class="row mt-4">
            <?php if ($profiles): ?>
                <?php foreach ($profiles as $profile): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card profile-card">
                            <img src="assets/images/<?php echo htmlspecialchars($profile['profile_picture']); ?>" alt="Profile Picture" class="card-img-top">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($profile['name']); ?></h5>
                                <p class="card-text">
                                    <strong>Address:</strong> <?php echo htmlspecialchars($profile['address']); ?><br>
                                    <strong>Contact No:</strong> <?php echo htmlspecialchars($profile['contact_number']); ?><br>
                                    <strong>Joined:</strong> <?php echo date("F d, Y", strtotime($profile['created_at'])); ?>
                                </p>
                                <?php if ($curr_id == $profile['id']): ?>
                                    <a href="my_profile.php?id=<?php echo $curr_id; ?>" class="btn btn-secondary">View Profile</a>
                                <?php else: ?>
                                    <a href="view_profile.php?id=<?php echo $profile['id']; ?>" class="btn btn-primary">View Profile</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">No profiles found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>