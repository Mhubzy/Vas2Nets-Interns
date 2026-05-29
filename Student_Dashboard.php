<?php
//Authentication Protection
require_once __DIR__ ."/../Includes/Student_auth.php";

//Load USer class
require_once __DIR__ ."/../classes/User.php";

$userModel = new User();

//Get Logged in user data
$user = $userModel->findById($_SESSION['user_id']);

//Safe Fallback Values
$id            = $user['id']            ?? null;
$first         = $user['first']         ??  'User';
$last          = $user['last']          ?? '';
$email         = $user['email']         ?? '';
$profile_image = $user['profile_image'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - School Portal</title>
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

        <div class="welcome-card">
            <h2>Welcome, <?php echo htmlspecialchars($first ?? 'user'); ?>! 👋</h2>
            <p>You are logged in as: <strong><?php echo htmlspecialchars($email ?? 'Guest'); ?></strong></p>
        </div>
     
        <div class="profile-image">
            <?php 
            $image_path = __DIR__ . '/../uploads/Student_images/' . $profile_image;
            if (!empty($profile_image) && is_file($image_path)) : 
            ?>
                <img src="../uploads/Student_images/<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Picture">
            <?php else : ?>
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%23ddd' width='100' height='100'/%3E%3Ctext x='50%25' y='50%25' font-size='50' fill='%23999' text-anchor='middle' dy='.3em'%3EUser%3C/text%3E%3C/svg%3E" alt="Default Avatar">
            <?php endif; ?>
        </div>

        <div class="user-info">
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars(($first ?? ''). ' '.($last ?? '')); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Matric Number:</strong> STU-<?php echo !empty($id) ? str_pad($id, 5, '0', STR_PAD_LEFT) : '00000'; ?></p>
        </div>

        <h2>Quick Access</h2>

        <div class="dashboard-grid">

            <div class="card">
                <h3>📚 My Courses</h3>
                <p>View and manage your enrolled courses.</p>
                <a href="Course.php">View Courses</a>
            </div>

            <div class="card">
                <h3>👤 My Profile</h3>
                <p>Update your personal information and settings.</p>
                <a href="Profile.php">Edit Profile</a>
            </div>
            
            <div class="card">
                <h3>📝 Assignments</h3>
                <p>Submit your assignments here.</p>
                <a href="Assignments.php">View Assignments</a>
            </div>

            <div class="card">
                <h3>📊 Results</h3>
                <p>View your results here.</p>
                <a href="Results.php">View Results</a>
            </div>

        </div>    

        <h2 style="margin-top: 40px;">Recent Activity</h2>

        <div class="card">
            <p>✓ Successfully logged in</p>
            <p style="color: #999; font-size: 0.85em;"><?php echo date('F j, Y - g:i A'); ?></p>
        </div>

    </main>

</div>

<footer><p>&copy; 2026 School Portal by Mubarak</p></footer>

</body>
</html>
