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

// Flash messages
$success = $_SESSION['success'] ?? ''; 
$error   = $_SESSION['error']   ?? ''; 
unset($_SESSION['success'], $_SESSION['error']);

$assignments = $adminModel->getAllAssignments();
$total       = count($assignments);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Assignments – Admin Panel</title>
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
            <h2>📝 All Assignments</h2>
            <p>View and manage every assignment submission from all students.</p>
        </div>
 
        <div class="profile-section" style="padding:0; overflow:hidden;">
 
            <div style="padding:16px 20px; border-bottom:1px solid var(--border);
                        display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0;">
                    <?php echo $total; ?> Submission<?php echo $total !== 1 ? 's' : ''; ?>
                </h3>
            </div>
 
            <?php if (!empty($assignments)) : ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Course Title</th>
                            <th>Course Code</th>
                            <th>File</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($assignments as $i => $a) : ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo htmlspecialchars($a['first'] . ' ' . $a['last']); ?></td>
                            <td><?php echo htmlspecialchars($a['email']); ?></td>
                            <td><?php echo htmlspecialchars($a['course_title']); ?></td>
                            <td><?php echo htmlspecialchars($a['course_code']);  ?></td>
                            <td>
                                <a href="../uploads/Assignment_files/<?php echo htmlspecialchars($a['filename']); ?>"
                                   target="_blank" class="tbl-btn tbl-btn-view">📄 View</a>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($a['submitted_at'])); ?></td>
                            <td>
                                <form method="post" action="../actions/Admin_Actions/Admin_allassignment.php"
                                      style="display:inline;"
                                      onsubmit="return confirm('Delete this submission? This cannot be undone.');">
                                    <input type="hidden" name="action"        value="delete_assignment">
                                    <input type="hidden" name="assignment_id" value="<?php echo (int)$a['id']; ?>">
                                    <button type="submit" class="tbl-btn tbl-btn-delete">🗑 Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p class="empty-state">📭 No assignments submitted yet.</p>
            <?php endif; ?>
 
        </div>
 
    </main>
</div>
 
<footer><p>&copy; 2026 School Portal by Mubarak</p></footer>
 
</body>
</html>