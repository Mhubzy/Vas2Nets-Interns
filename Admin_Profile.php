<?php
//Authentication Protection
require_once __DIR__ . '/../Includes/Admin_auth.php';

//Load User class
require_once __DIR__ . '/../classes/User.php';

$userModel = new User();

//Logged in admin's own information
$admin = $userModel->findById($_SESSION['user_id']);

$id            =$admin['id']             ?? null;
$first         = $admin['first']         ?? 'Admin';
$last          = $admin['last']          ?? '';
$email         = $admin['email']         ?? '';
$profile_image = $admin['profile_image'] ?? '';

// Flash messages
$success = $_SESSION['success'] ?? ''; 
$error   = $_SESSION['error']   ?? ''; 
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile – Admin Panel</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
 
<header>
    <h1>SCHOOL <span>PORTAL</span></h1>
    <span class="header-role-badge admin">Admin Panel</span>
</header>
 
<div class="dashboard-container">
 
    <!-- Sidebar -->
    <aside class="sidebar admin">
        <h3>Admin Menu</h3>
        <ul>
            <li><a href="Admin_Dashboard.php">📊 Dashboard</a></li>
            <li><a href="All_Users.php">👥 All Users</a></li>
            <li><a href="Assign_Advisor.php">🔗 Assign Advisor</a></li>
            <li><a href="Create_Advisor.php">➕ Create Advisor</a></li>
            <li><a href="All_Assignments.php">📝 All Assignments</a></li>
            <li><a href="Assign_Courses.php">📚 Assign Courses</a></li>
            <li><a href="Create_Courses.php">➕ Create Courses</a></li>
            <li><a href="All_Results.php">📊 All Results</a></li>
            <li><a href="Admin_Profile.php">👤 My Profile</a></li>
        </ul>
        <div class="sidebar-divider"></div>
        <ul><li><a href="../Logout.php">🚪 Logout</a></li></ul>
    </aside>
 
    <main class="main-content">
 
        <?php if ($success) : ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error) : ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
 
        <div class="admin-banner">
            <h2>👤 My Profile</h2>
            <p>Update your admin account information and password.</p>
        </div>
 
        <!-- Profile Picture -->
        <div class="profile-image lg" style="margin:10px 0;">
            <?php
            $img_path = __DIR__ . '/../uploads/Admin_images/' . $profile_image;
            if (!empty($profile_image) && is_file($img_path)) : ?>
                <img src="../uploads/Admin_images/<?php echo htmlspecialchars($profile_image); ?>"
                     alt="Profile Picture">
            <?php else : ?>
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%23ddd' width='100' height='100'/%3E%3Ctext x='50%25' y='50%25' font-size='50' fill='%23999' text-anchor='middle' dy='.3em'%3EA%3C/text%3E%3C/svg%3E"
                     alt="Default Avatar">
            <?php endif; ?>
        </div>
 
        <div class="user-info">
            <p><strong>Full Name:</strong>  <?php echo htmlspecialchars($first . ' ' . $last); ?></p>
            <p><strong>Email:</strong>      <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Role:</strong>       <span class="badge badge-admin">Admin</span></p>
            <p><strong>Admin ID:</strong>   ADM-<?php echo str_pad($id ?? 0, 5, '0', STR_PAD_LEFT); ?></p>
        </div>
 
        <!-- Upload Photo -->
        <div class="profile-section">
            <h3>📷 Update Profile Photo</h3>
            <form method="post" action="../actions/Admin_Profile.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_photo">
                <label for="photo">Choose a photo (JPG, PNG, GIF, WEBP — max 2 MB)</label>
                <input type="file" name="photo" id="photo" accept="image/*" required>
                <button type="submit" class="submit-btn">📤 Upload Photo</button>
            </form>
        </div>
 
        <!-- Edit Name -->
        <div class="profile-section">
            <h3>✏️ Edit Name</h3>
            <form method="post" action="../actions/Admin_Profile.php">
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
            <form method="post" action="../actions/Admin_Profile.php">
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