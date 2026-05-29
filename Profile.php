<?php
//Authentication Protection
require_once __DIR__ ."/../Includes/Student_auth.php";

//Load USer class
require_once __DIR__ ."/../classes/User.php";

$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);

$first          = $user['first']            ?? '';
$last           = $user['last']             ?? '';
$email          = $user['email']            ?? '';
$profile_image  = $user['profile_image']    ?? '';
$id             = $user['id']               ?? 0;

$message = '';

if(isset($_SESSION['success'])) {
    $message = "<p style = 'color:green'>" .$_SESSION['success'] ."</p>";
    unset($_SESSION['success']);
}

if(isset($_SESSION['error'])) {
    $message = "<p style = 'color:red'>" .$_SESSION['error'] ."</p>";
    unset($_SESSION['error']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - School Portal</title>
    <link rel="stylesheet" href="../style.css"> 
</head>
<body>
<header><h1>SCHOOL PORTAL</h1></header>

<div class="dashboard-container">

    <!-- Sidebar -->
    <aside class="sidebar">
        <h3>Menu</h3>
        <ul>
            <li><a href="Student_Dashboard.php">📊 Dashboard</a></li>
            <li><a href="Course.php">📚 My Courses</a></li>
            <li><a href="Profile.php">👤 My Profile</a></li>
            <li><a href="Assignments.php">📝 Assignments</a></li>
            <li><a href="Results.php" target="_blank">📊 Results</a></li>
            <li><a href="../Logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <h2>👤 My Profile</h2>
        
        <!-- Success / Error message -->
        <?php echo $message; ?>

        <!-- Profile Picture + info summary -->
        <div class="profile-image">
            <?php 
            $image_path = __DIR__ . '/../uploads/Student_images/' . $profile_image;
            if (!empty($profile_image) && is_file($image_path)) : 
            ?>
                <img src="../uploads/Student_images/<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Picture">
            <?php else : ?>
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 150 150'%3E%3Crect fill='%23ddd' width='150' height='150'/%3E%3Ctext x='50%25' y='50%25' font-size='40' fill='%23999' text-anchor='middle' dy='.3em'%3EUser%3C/text%3E%3C/svg%3E" alt="Default Avatar">
            <?php endif; ?>
        </div>

        <div class="user-info">
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($first . ' ' . $last); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Student ID:</strong> STU-<?php echo str_pad($id ?? 0, 5, '0', STR_PAD_LEFT); ?></p>
        </div>

        <!-- Upload Profile Photo enctype="multipart/form-data" is REQUIRED for file uploads -->
        <div class="profile-section">
            <h3>📷 Update Profile Photo</h3>
            <form method="post" action="../actions/Student_Actions/Update_Profile.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_photo">
                <label for="photo">Choose a photo (JPG, PNG, GIF, WEBP — max 2MB)</label>
                <input type="file" name="photo" id="photo" accept="image/*" required>
                <br>
                <button type="submit" class="submit-btn" style="margin-top:10px;">📤 Upload Photo</button>
            </form>
        </div>

        <!-- Edit Name -->
        <div class="profile-section">
            <h3>✏️ Edit Name</h3>
            <form method="post" action="../actions/Student_Actions/Update_Profile.php" >
                <input type="hidden" name="action" value="update_name">

                <label for="first">First Name *</label>
                <input type="text" name="first" id="first"
                       value="<?php echo htmlspecialchars($first); ?>" required>

                <label for="last">Last Name *</label>
                <input type="text" name="last" id="last"
                       value="<?php echo htmlspecialchars($last); ?>" required>

                <button type="submit" class="submit-btn">💾 Save Name</button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="profile-section">
            <h3>🔒 Change Password</h3>
            <form method="post" action="../actions/Student_Actions/Update_Profile.php" >
                <input type="hidden" name="action" value="change_password">

                <label for="current_pwd">Current Password *</label>
                <input type="password" name="current_pwd" id="current_pwd" required>

                <label for="new_pwd">New Password * (min. 6 characters)</label>
                <input type="password" name="new_pwd" id="new_pwd" required>

                <label for="confirm_pwd">Confirm New Password *</label>
                <input type="password" name="confirm_pwd" id="confirm_pwd" required>

                <button type="submit" class="submit-btn">🔑 Change Password</button>
            </form>
        </div>

    </main>
</div>

<footer><p>&copy; 2026 School Portal by Mubarak</p></footer>

</body>
</html>
