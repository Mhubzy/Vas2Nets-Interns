<?php
//Authentication Protection
require_once __DIR__ . '/../Includes/Advisor_auth.php';

//Load Advisor class
require_once __DIR__ . '/../classes/Advisor.php';

//Load User class
require_once __DIR__ . '/../classes/User.php';
 
$advisorModel = new Advisor();
$userModel    = new User();
 
$advisor       = $userModel->findById($_SESSION['user_id']);
$advisor_id    = (int) $_SESSION['user_id'];
$first         = $advisor['first']         ?? 'Advisor';
$last          = $advisor['last']          ?? '';
$profile_image = $advisor['profile_image'] ?? '';
 
$totalStudents    = $advisorModel->getTotalMyStudents($advisor_id);
$totalAssignments = $advisorModel->getTotalMyAssignments($advisor_id);
$totalCourses     = $advisorModel->getTotalMyCourses($advisor_id);
$totalGraded      = $advisorModel->getTotalGraded($advisor_id);

//Recent activity
$recentStudents   = $advisorModel->getRecentMyStudents($advisor_id, 5);
$recentAssignments = $advisorModel->getRecentMyAssignments($advisor_id, 5);
 
$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error']   ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advisor Dashboard – School Portal</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
 
<header>
    <h1>SCHOOL <span>PORTAL</span></h1>
    <span class="header-role-badge advisor">Advisor Panel</span>
</header>
 
<div class="dashboard-container">
 
    <aside class="sidebar advisor">
        <h3>Advisor Menu</h3>
        <ul>
            <li><a href="Advisor_Dashboard.php">📊 Dashboard</a></li>
            <li><a href="Advisor_Students.php">🎓 My Students</a></li>
            <li><a href="Advisor_Courses.php">📚 My Courses</a></li>
            <li><a href="Advisor_Assignments.php">📝 Assignments</a></li>
            <li><a href="Upload_Results.php">📊 Upload Results</a></li>
            <li><a href="Advisor_Profile.php">👤 My Profile</a></li>
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
 
        <!-- Welcome Banner -->
        <div class="advisor-banner">

            <div style="display:flex; align-items:center; gap:16px;">
                <div class="profile-image" style="margin:0;">
                    <?php
                    $img = __DIR__ . '/../uploads/Advisor_images/' . $profile_image;
                    if (!empty($profile_image) && is_file($img)) : ?>
                        <img src="../uploads/Advisor_images/<?php echo htmlspecialchars($profile_image); ?>"
                             style="width:60px;height:60px;" alt="Profile">
                    <?php else : ?>
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%23ddd' width='100' height='100'/%3E%3Ctext x='50%25' y='50%25' font-size='50' fill='%23999' text-anchor='middle' dy='.3em'%3EAD%3C/text%3E%3C/svg%3E"
                             style="width:60px;height:60px;" alt="Default">
                    <?php endif; ?>
                </div>
                <div>
                    <h2>Welcome back, <?php echo htmlspecialchars($first); ?>! 👋</h2>
                    <p>Here is an overview of your students — <?php echo date('l, F j, Y'); ?></p>
                </div>
            </div>
        </div>
 
        <!-- Stats -->
        <div class="dashboard-grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">

            <div class="stat-card">
                <div class="stat-icon">🎓</div>
                <div><div class="stat-value"><?php echo $totalStudents; ?></div><div class="stat-label">My Students</div></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <div><div class="stat-value"><?php echo $totalAssignments; ?></div><div class="stat-label">Assignments</div></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div><div class="stat-value"><?php echo $totalCourses; ?></div><div class="stat-label">Courses</div></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div><div class="stat-value"><?php echo $totalGraded; ?></div><div class="stat-label">Graded</div></div>
            </div>

        </div>
 
        <!-- Quick Access -->
        <h2 class="section-title">Quick Access</h2>
        <div class="dashboard-grid">

            <div class="card">
                <h3>🎓 My Students</h3>
                <p>View all students assigned to you.</p>
                <a href="Advisor_Students.php">View Students</a>
            </div>
            
            <div class="card">
                <h3>📝 Assignments</h3>
                <p>View and grade all student submissions.</p>
                <a href="Advisor_Assignments.php">View Assignments</a>
            </div>
                  
            <div class="card">
                <h3>Upload Results</h3>
                <p>Upload student results.</p>
                <a href="Upload_Results.php">Upload Results</a>
            </div>
            
            <div class="card">
                <h3>👤 My Profile</h3>
                <p>Update your profile information.</p>
                <a href="Advisor_Profile.php">Edit Profile</a>
            </div>

        </div>
 
        <!-- Recent Students -->
        <h2 class="section-title" style="margin-top:10px;">Recently Assigned Students</h2>
        <div class="profile-section" style="padding:0; overflow:hidden;">

            <?php if (!empty($recentStudents)) : ?>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Assigned</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentStudents as $s) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['first'] . ' ' . $s['last']); ?></td>
                            <td><?php echo htmlspecialchars($s['email']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($s['assigned_at'])); ?></td>
                            <td>
                                <a href="Student_Details.php?id=<?php echo (int)$s['id']; ?>"
                                   class="tbl-btn tbl-btn-view">👁 View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else : ?>
                <p class="empty-state">📭 No students assigned yet.</p>
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
                            <th>Code</th>
                            <th>Grade</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentAssignments as $a) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($a['first'] . ' ' . $a['last']); ?></td>
                            <td><?php echo htmlspecialchars($a['course_title']); ?></td>
                            <td><?php echo htmlspecialchars($a['course_code']); ?></td>
                            <td>
                                <?php if (!empty($a['score'])) : ?>
                                    <span class="badge badge-approved"><?php echo htmlspecialchars($a['score']); ?></span>
                                <?php else : ?>
                                    <span class="badge badge-pending">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($a['submitted_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else : ?>
                <p class="empty-state">📭 No assignments yet.</p>
            <?php endif; ?>

        </div>
 
    </main>
</div>
 
<footer><p>&copy; 2026 School Portal by Mubarak</p></footer>
</body>
</html>
 