<?php
//Authentication Protection
require_once __DIR__ . '/../Includes/Admin_auth.php';

//Load Admin class
require_once __DIR__ . '/../classes/Admin.php';

//Load User class
require_once __DIR__ . '/../classes/User.php';

$adminModel = new Admin();
$userModel = new User();

//Logged in admin's own information
$admin = $userModel->findById($_SESSION['user_id']);
$first         = $admin['first']         ?? 'Admin';
$profile_image = $admin['profile_image'] ?? '';

//Admin to have access to student's result
$results = $adminModel->getAllResults();

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
    <title>Admin Dashboard – School Portal</title>
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
 
    <!-- Main Content -->
    <main class="main-content">
 
        <?php if ($success) : ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error) : ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
 
        <!-- Welcome Banner -->
        <div class="admin-banner">
            <div style="display:flex; align-items:center; gap:16px;">
                <div class="profile-image" style="margin:0;">
                    <?php
                    $img_path = __DIR__ . '/../uploads/Admin_images/' . $profile_image;
                    if (!empty($profile_image) && is_file($img_path)) : ?>
                        <img src="../uploads/Admin_images/<?php echo htmlspecialchars($profile_image); ?>"
                             alt="Profile" style="width:60px;height:60px;">
                    <?php else : ?>
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%23ddd' width='100' height='100'/%3E%3Ctext x='50%25' y='50%25' font-size='50' fill='%23999' text-anchor='middle' dy='.3em'%3EA%3C/text%3E%3C/svg%3E"
                             alt="Admin" style="width:60px;height:60px;">
                    <?php endif; ?>
                </div>

                <div>
                    <h2>Welcome back, <?php echo htmlspecialchars($first); ?>! 👋</h2>
                    <p>Here's what's happening in your portal today —
                       <?php echo date('l, F j, Y'); ?></p>
                </div>
                
            </div>
        </div>

        <!-- Admin to view all student's results -->
        <h2 class="section-title" style="margin-top:10px;">All Students results</h2>

        <div class="profile-section" style="padding:0; overflow:hidden;">
            
            <table class="data-table">
            
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Advisor</th>
                        <th>Score</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php foreach ($results as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['student_first']. ' ' . $r['student_last']); ?></td>
                            <td><?php echo htmlspecialchars($r['course_title']); ?></td>
                            <td><?php echo htmlspecialchars($r['advisor_first']. ' ' .$r['advisor_last']); ?></td>
                            <td><?php echo htmlspecialchars($r['score']); ?></td>
                            <td><?php echo htmlspecialchars($r['grade_letter']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
           
            </table>

        </div>

        <!-- Admin to release results -->
        <form method="POST" action="../actions/Admin_Actions/Admin_ReleaseResults.php">
            <input type="hidden" name="action" value="release_results">
            <button type="submit">Release Results</button>
        </form>

        <form method="POST" action="../actions/Admin_Actions/Admin_ReleaseResults.php">
            <input type="hidden" name="action" value="hide_results">
            <button type="submit">Hide Results</button>
        </form>

    </main>

</div>

</body>
</html>
