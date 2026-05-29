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

// Flash messages
$success = $_SESSION['success'] ?? ''; 
$error   = $_SESSION['error']   ?? ''; 
unset($_SESSION['success'], $_SESSION['error']);

$courses = $adminModel->getAllCourses();
$advisors = $adminModel->getUsersByRole('advisor');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Courses – Admin Panel</title>
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
            <h2>📚 All Courses</h2>
            <p>View all courses and assign them to various advisors.</p>
        </div>

        <?php if (empty($courses)) : ?>
            <div class="alert alert-warning">
                ⚠️ No course exist yet.
                <a href="Create_Courses.php" style="font-weight:700; margin-left:6px;">
                    Create a course first →
                </a>
            </div>
        <?php endif; ?>
 
        <div class="profile-section" style="padding:0; overflow:hidden;">

            <div style="padding:16px 20px; border-bottom:1px solid var(--border);">
                <h3 style="margin:0;">All Students & Their Assigned Advisors</h3>
            </div>
 
            <?php if (!empty($courses)) : ?>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Course Title</th>
                            <th>Course Code</th>
                            <th>Description</th>
                            <th>Assign / change Advisor</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($courses as $i => $c) : ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            
                            <td>
                                <strong><?php echo htmlspecialchars($c['course_title']); ?></strong><br>
                            </td>
                            
                            <td>
                                <?php echo htmlspecialchars($c['course_code']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($c['description']); ?>
                            </td>

                            
                            <td>
                                <?php if (!empty($c['advisor_first'])) : ?>
                                    <span class="badge badge-advisor">
                                        <?php echo htmlspecialchars($c['advisor_first'] . ' ' . $c['advisor_last']); ?>
                                    </span>
                                <?php else : ?>
                                    <span class="badge badge-pending">Not Assigned</span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php if (!empty($advisors)) : ?>
                                <form method="post" action="../actions/Admin_Actions/Admin_AssignCourses.php"
                                      style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                                    <input type="hidden" name="action"     value="assign_course">
                                    <input type="hidden" name="course_id" value="<?php echo (int)$c['id']; ?>">
                                    <select name="advisor_id"
                                            style="padding:5px 8px; border-radius:4px; font-size:0.82rem;
                                                   border:1px solid var(--border); margin-bottom:0; width:auto;">
                                        <option value="">-- Select Advisor --</option>
                                        <?php foreach ($advisors as $adv) : ?>
                                            <option value="<?php echo (int)$adv['id']; ?>"
                                                <?php echo ((int)($c['advisor_id'] ?? 0) === (int)$adv['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($adv['first'] . ' ' . $adv['last']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="tbl-btn tbl-btn-edit">Assign</button>
                                </form>
                                <?php else : ?>
                                    <span style="font-size:0.8rem;color:var(--text-muted);">No advisors yet</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if (!empty($c['advisor_first'])) : ?>
                                <form method="post" action="../actions/Admin_Actions/Admin_AssignCourses.php"
                                      style="display:inline;"
                                      onsubmit="return confirm('Remove advisor from <?php echo htmlspecialchars($c['course_title']); ?>?');">
                                    <input type="hidden" name="action"     value="unassign_course">
                                    <input type="hidden" name="course_id" value="<?php echo (int)$c['id']; ?>">
                                    <button type="submit" class="tbl-btn tbl-btn-delete">✖ Remove</button>
                                </form>
                                <?php else : ?>
                                    <span style="color:var(--text-muted);">—</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <form method="post" action="../actions/Admin_Actions/Admin_AssignCourses.php" 
                                    style="display:inline;"
                                    onsubmit="return confirm('Permanently delete course <?php echo htmlspecialchars($c['course_title']);?>?');">
                                    <input type="hidden" name="action"    value="delete_course">
                                    <input type="hidden" name="course_id" value="<?php echo (int)$c['id']; ?>">
                                    <button type="submit" class="tbl-btn tbl-btn-delete">🗑 Delete</button>
                                </form>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else : ?>
                <p class="empty-state">📭 No course assigned yet.</p>
            <?php endif; ?>

        </div>
 
    </main>
</div>
 
<footer><p>&copy; 2026 School Portal by Mubarak</p></footer>
 
</body>
</html>