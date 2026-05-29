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
$admin         = $userModel->findById($_SESSION['user_id']);
$first         = $admin['first']         ?? 'Ad min';
$last          = $admin['last']          ?? '';
$profile_image = $admin['profile_image'] ?? '';

//Dashboard stats
$totalStudents    = $adminModel->getTotalByRole('student');
$totalAdvisors    = $adminModel->getTotalByRole('advisor');
$totalAdmins      = $adminModel->getTotalByRole('admin');
$totalCourses     = $adminModel->getTotalCourses();
$totalAssignments = $adminModel->getTotalAssignments();
$totalUnassigned  = $adminModel->getTotalUnassignedStudents();

//Recent Activity
$recentUsers       = $adminModel->getRecentUsers(5);
$recentAssignments = $adminModel->getRecentAssignments(5);

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
            <li><a href="Create_Advisor.php">➕ Create Advisor</a></li>
            <li><a href="Assign_Advisor.php">🔗 Assign Advisor</a></li>
            <li><a href="All_Assignments.php">📝 All Assignments</a></li>
            <li><a href="Create_Courses.php">➕ Create Courses</a></li>
            <li><a href="Assign_Courses.php">📚 Assign Courses</a></li>
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
 
        <!-- Stats Cards -->
        <div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px,1fr));">
 
            <div class="stat-card">
                <div class="stat-icon">🎓</div>
                <div>
                    <div class="stat-value"><?php echo $totalStudents; ?></div>
                    <div class="stat-label">Students</div>
                </div>
            </div>
         
            <div class="stat-card">
                <div class="stat-icon">🧑‍🏫</div>
                <div>
                    <div class="stat-value"><?php echo $totalAdvisors; ?></div>
                    <div class="stat-label">Advisors</div>
                </div>
            </div>
 
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div>
                    <div class="stat-value"><?php echo $totalCourses; ?></div>
                    <div class="stat-label">Courses</div>
                </div>
            </div>
 
            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <div>
                    <div class="stat-value"><?php echo $totalAssignments; ?></div>
                    <div class="stat-label">Assignments</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">⚠️</div>
                <div>
                    <div class="stat-value" style="color: <?php echo $totalUnassigned > 0 ? 'var(--primary)': 'inherit'; ?>;">
                        <?php echo $totalUnassigned; ?></div>
                    <div class="stat-label">Unassigned</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🛡️</div>
                <div>
                    <div class="stat-value"><?php echo $totalAdmins; ?></div>
                    <div class="stat-label">Admins</div>
                </div>
            </div>
 
        </div>

        <!-- Total Number of Unassigned students -->
        <?php if($totalUnassigned > 0): ?>
            <div class="alert alert-warning">
                ⚠️<strong><?php echo $totalUnassigned; ?> student<?php echo $totalUnassigned !== 1 ? 's':''; ?></strong>
            </div> 
            Not yet assigned to an advisor <a href="../actions/Admin_Actions/Admin_AssignAdvisor.php" 
            style="font-weight:700; margin-left:6px;">Assign now→</a>
        <?php endif; ?>
 
        <!-- Quick Access -->
        <h2 class="section-title">Quick Access</h2>
        <div class="dashboard-grid">

            <div class="card">
                <h3>👥 Manage Users</h3>
                <p>View, search, change roles and delete users.</p>
                <a href="All_Users.php">View All Users</a>
            </div>
            
            <div class="card">
                <h3>➕ Create Advisor</h3>
                <p>Create a new advisor account for the portal.</p>
                <a href="Create_Advisor.php">Create Advisor</a>
            </div>

            <div class="card">
                <h3>🔗 Assign Advisor</h3>
                <p>Assign students to advisors so they can be graded.</p>
                <a href="Assign_Advisor.php">Assign Now</a>
            </div>


            <div class="card">
                <h3>📝 Assignments</h3>
                <p>View and manage all student submissions.</p>
                <a href="All_Assignments.php">View All Assignments</a>
            </div>

            <div class="card">
                <h3>➕ Create Courses</h3>
                <p>Create courses.</p>
                <a href="Create_Courses.php">Create Courses</a>
            </div>

            <div class="card">
                <h3>🔗 Assign Courses</h3>
                <p>Assign advisors to created courses.</p>
                <a href="Assign_Courses.php">Assign Courses</a>
            </div>

      
            <div class="card">
                <h3>📊 Results</h3>
                <p>View and manage all student results.</p>
                <a href="All_Results.php">View All Results</a>
            </div>

        </div>
 
        <!-- Recent Users -->
        <h2 class="section-title" style="margin-top:10px;">Recently Registered Users</h2>
        
        <div class="profile-section" style="padding:0; overflow:hidden;">
            <?php if (!empty($recentUsers)) : ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentUsers as $u) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($u['first'] . ' ' . $u['last']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><span class="badge badge-<?php echo htmlspecialchars($u['role']); ?>">
                                <?php echo ucfirst(htmlspecialchars($u['role'])); ?>
                            </span></td>
                            <td><?php echo htmlspecialchars(date('M j, Y', strtotime($u['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p class="empty-state">No users registered yet.</p>
            <?php endif; ?>
        </div>
 
        <!-- Recent Assignments -->
        <h2 class="section-title" style="margin-top:10px;">Recent Assignment Submissions</h2>

        <div class="profile-section" style="padding:0; overflow:hidden;">
            <?php if (!empty($recentAssignments)) : ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Course Id</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentAssignments as $a) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($a['first'] . ' ' . $a['last']); ?></td>
                            <td><?php echo htmlspecialchars($a['course_title']); ?></td>
                            <td><?php echo htmlspecialchars($a['course_code']);  ?></td>
                            <td><?php echo htmlspecialchars(date('M j, Y', strtotime($a['submitted_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p class="empty-state">No assignments submitted yet.</p>
            <?php endif; ?>
        </div>
 
    </main>
    
</div>
 
<footer><p>&copy; 2026 School Portal by Mubarak</p></footer>
 
</body>
</html>