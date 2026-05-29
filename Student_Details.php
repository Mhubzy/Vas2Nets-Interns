<?php
//Authentication Protection
require_once __DIR__ . '/../Includes/Advisor_auth.php';

//Load Advisor class
require_once __DIR__ . '/../classes/Advisor.php';

//Load User class
require_once __DIR__ . '/../classes/User.php';
 
$advisorModel = new Advisor();
$advisor_id   = (int) $_SESSION['user_id'];
 
$student_id = (int) ($_GET['id'] ?? 0);
if ($student_id <= 0) {
    header("Location: Advisor_Students.php");
    exit;
}
 
// Verify this student belongs to this advisor
$student = $advisorModel->getMyStudentById($advisor_id, $student_id);
if (!$student) {
    $_SESSION['error'] = "Student not found or not assigned to you.";
    header("Location: Advisor_Students.php");
    exit;
}
 
$courses     = $advisorModel->getStudentCourses($advisor_id, $student_id);
$assignments = $advisorModel->getStudentAssignments($advisor_id, $student_id);
 
$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error']   ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($student['first']); ?>'s Profile – Advisor Panel</title>
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
 
        <!-- Back link -->
        <p style="margin-bottom:12px;">
            <a href="Advisor_Students.php" style="color:var(--text-muted); font-size:0.88rem;">
                ← Back to My Students
            </a>
        </p>
 
        <!-- Student Profile Card -->
        <div class="advisor-banner">
            <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
                <?php
                $img = __DIR__ . '/../uploads/Advisor_images/' . ($student['profile_image'] ?? '');
                if (!empty($student['profile_image']) && is_file($img)) : ?>
                    <img src="../uploads/Advisor_images/<?php echo htmlspecialchars($student['profile_image']); ?>"
                         style="width:80px;height:80px;border-radius:50%;object-fit:cover;
                                border:3px solid rgba(255,255,255,0.4);" alt="Profile">
                <?php else : ?>
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%23ddd' width='100' height='100'/%3E%3Ctext x='50%25' y='50%25' font-size='50' fill='%23999' text-anchor='middle' dy='.3em'%3EU%3C/text%3E%3C/svg%3E"
                         style="width:80px;height:80px;border-radius:50%;" alt="Default">
                <?php endif; ?>
                <div>
                    <h2><?php echo htmlspecialchars($student['first'] . ' ' . $student['last']); ?></h2>
                    <p><?php echo htmlspecialchars($student['email']); ?></p>
                    <p>Student ID: STU-<?php echo str_pad($student['id'], 5, '0', STR_PAD_LEFT); ?></p>
                </div>
            </div>
        </div>
 
        <!-- Registered Courses -->
        <h2 class="section-title">📚 Registered Courses (<?php echo count($courses); ?>)</h2>
        <div class="profile-section" style="padding:0; overflow:hidden;">

            <?php if (!empty($courses)) : ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Course Title</th>
                            <th>Course Code</th>
                            <th>Description</th>
                            <th>Enrolled</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($courses as $i => $c) : ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo htmlspecialchars($c['course_title']); ?></td>
                            <td><?php echo htmlspecialchars($c['course_code']);  ?></td>
                            <td><?php echo htmlspecialchars(strlen($c['description'] ?? '') > 60
                                    ? substr($c['description'], 0, 60) . '...'
                                    : ($c['description'] ?? '—')); ?></td>
                            <td><?php echo date('M j, Y', strtotime($c['enrolled_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p class="empty-state">📭 No courses registered yet.</p>
            <?php endif; ?>

        </div>
 
        <!-- Assignments + Grading -->
        <h2 class="section-title" style="margin-top:10px;">
            📝 Submitted Assignments (<?php echo count($assignments); ?>)
        </h2>
 
        <?php if (!empty($assignments)) : ?>
            
            <?php foreach ($assignments as $a) : ?>
            <div class="profile-section" style="margin-bottom:16px;">

                <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">

                    <div>
                        <h3 style="margin:0 0 6px;"><?php echo htmlspecialchars($a['course_title']); ?>
                            <small style="font-weight:400; color:var(--text-muted);">
                                (<?php echo htmlspecialchars($a['course_code']); ?>)
                            </small>
                        </h3>
                        <p style="font-size:0.85rem; color:var(--text-muted); margin:3px 0;">
                            Submitted: <?php echo date('M j, Y – g:i A', strtotime($a['submitted_at'])); ?>
                        </p>
                        <a href="../uploads/Assignment_files/<?php echo htmlspecialchars($a['filename']); ?>"
                           target="_blank" class="tbl-btn tbl-btn-view" style="margin-top:6px; display:inline-block;">
                            📄 View File
                        </a>
                    </div>

                    <div>
                        <?php if (!empty($a['score'])) : ?>
                            <span class="badge badge-approved" style="font-size:0.9rem; padding:5px 14px;">
                                Grade: <?php echo htmlspecialchars($a['score']); ?>
                            </span>
                        <?php else : ?>
                            <span class="badge badge-pending">Not Graded Yet</span>
                        <?php endif; ?>
                    </div>

                </div>
 
                <?php if (!empty($a['comment'])) : ?>

                    <div style="margin-top:12px; background:var(--navy-light); padding:10px 14px;
                                border-radius:var(--radius); font-size:0.88rem;">
                        <strong>Your Feedback:</strong>
                        <?php echo htmlspecialchars($a['comment']); ?>
                        <span style="color:var(--text-muted); font-size:0.78rem; margin-left:8px;">
                            (<?php echo date('M j, Y', strtotime($a['graded_at'])); ?>)
                        </span>
                    </div>

                <?php endif; ?>
 
                <!-- Grade Form -->
                <form method="POST" action="../actions/Advisor_Grade.php"
                style="margin-top:14px; display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">

                    <input type="hidden" name="assignment_id" value="<?php echo (int)$a['id']; ?>">
                    <input type="hidden" name="redirect"      value="Student_Details.php?id=<?php echo $student_id; ?>">
                    <div>
                        <label style="font-size:0.82rem; font-weight:600; display:block; margin-bottom:4px;">
                            Score / Grade *
                        </label>
                        <input type="text" name="score" value="<?php echo htmlspecialchars($a['score'] ?? ''); ?>"
                        placeholder="e.g. A, B+, 85/100" style="width:160px; margin-bottom:0;">
                    </div>

                    <div style="flex:1; min-width:200px;">
                        <label style="font-size:0.82rem; font-weight:600; display:block; margin-bottom:4px;">
                            Feedback / Comment
                        </label>
                        <textarea name="comment" rows="2" placeholder="Leave a comment for the student..."
                        style="margin-bottom:0;"><?php echo htmlspecialchars($a['comment'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="submit-btn" style="margin:0;">
                        💾 <?php echo !empty($a['score']) ? 'Update Grade' : 'Save Grade'; ?>
                    </button>

                </form>

            </div>

            <?php endforeach; ?>
        <?php else : ?>
            <div class="profile-section">
                <p class="empty-state">📭 No assignments submitted yet.</p>
            </div>

        <?php endif; ?>
 
    </main>
</div>
 
<footer><p>&copy; 2026 School Portal by Mubarak</p></footer>
</body>
</html>
 