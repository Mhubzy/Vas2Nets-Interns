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

$courses = $advisorModel->getMyCourses($advisor_id);
$total   = count($courses);

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error']   ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses – Advisor Panel</title>
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
            <h2>📚 My Courses</h2>
            <p>All courses assigned to you. Click View Students to see who is enrolled.</p>
        </div>

        <div class="profile-section" style="padding:0; overflow:hidden;">

            <div style="padding:16px 20px; border-bottom:1px solid var(--border);">
                <h3 style="margin:0;">
                    <?php echo $total; ?> Course<?php echo $total !== 1 ? 's' : ''; ?>
                </h3>
            </div>

            <?php if (!empty($courses)) : ?>

                <table class="data-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Course Title</th>
                            <th>Course Code</th>
                            <th>Description</th>
                            <th>Created</th>
                            <th>Enrolled Students</th>
                        </tr>
                    </thead>

                    <tbody>
                    
                        <?php foreach ($courses as $i => $c) : ?>

                            <tr>

                                <td><?php echo $i + 1; ?></td>
                                <td><strong><?php echo htmlspecialchars($c['course_title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($c['course_code']); ?></td>
                                <td><?php echo htmlspecialchars($c['description'] ?? '—'); ?></td>
                                <td><?php echo date('M j, Y', strtotime($c['created_at'])); ?></td>
                                <td>

                                    <?php
                                    // Count students enrolled in this course
                                    $enrolled = $advisorModel->getStudentInCourse($advisor_id, $c['id']);
                                    if(!empty($enrolled)) :
                                    ?>

                                        <details>
                                            <summary style="cursor:pointer; font-weight: 600">
                                                <?php echo count($enrolled); ?> student<?php echo count($enrolled) !== 1 ? 's' : ''; ?>
                                            </summary>
                                            <ul style="margin:6px 0 0 16px; padding:0; font-size:0.85rem;">
                                                <?php foreach($enrolled as $stu) : ?>
                                                    <li>
                                                        <a href="Student_Details.php?id=<?php echo (int)$stu['id']; ?>" 
                                                        style="color: var(--primary);">
                                                         <?php echo htmlspecialchars($stu['first']. ' ' .$stu['last']); ?>   
                                                        </a>
                                                        <small style="color: var(--text-muted);">
                                                            - <?php echo htmlspecialchars($stu['email']); ?>
                                                        </small>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </details>

                                    <?php else : ?>
                                        <span style="color:var(--text-muted); font-size:0.85rem;">No students yet</span>
                                    <?php endif; ?>

                                </td>
                                
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            <?php else : ?>
                <p class="empty-state">📭 No courses assigned to you yet. Contact your admin.</p>
            <?php endif; ?>

        </div>

    </main>
    
</div>

<footer><p>&copy; 2026 School Portal by Mubarak</p></footer>
</body>
</html>