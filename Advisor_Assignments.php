<?php
//Authentication Protection
require_once __DIR__ . '/../Includes/Advisor_auth.php';

//Load Advisor class
require_once __DIR__ . '/../classes/Advisor.php';

//Load User class
require_once __DIR__ . '/../classes/User.php';
 
$advisorModel = new Advisor();
$userModel    = new User();
$advisor_id   = (int) $_SESSION['user_id'];
 
$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error']   ?? '';
unset($_SESSION['success'], $_SESSION['error']);
 
$assignments = $advisorModel->getAllMyAssignments();
$total       = count($assignments);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments – Advisor Panel</title>
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
 
        <div class="advisor-banner">
            <h2>📝 All Assignments</h2>
            <p>View and grade all assignment submissions from your students.</p>
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
                            <th>Course Title</th>
                            <th>Code</th>
                            <th>File</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($assignments as $i => $a) : ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($a['first'] . ' ' . $a['last']); ?></strong><br>
                                <small style="color:var(--text-muted);"><?php echo htmlspecialchars($a['email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($a['course_title']); ?></td>
                            <td><?php echo htmlspecialchars($a['course_code']);  ?></td>
                            <td>
                                <a href="../uploads/Assignment_files/<?php echo htmlspecialchars($a['filename']); ?>"
                                   target="_blank" class="tbl-btn tbl-btn-view">📄 View</a>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($a['submitted_at'])); ?></td>
                        </tr>

                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p class="empty-state">📭 No assignments submitted by your students yet.</p>
            <?php endif; ?>
 
        </div>
 
    </main>

</div>
 
<footer><p>&copy; 2026 School Portal by Mubarak</p></footer>

</body>
</html>