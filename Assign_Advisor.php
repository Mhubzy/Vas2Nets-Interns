<?php
//Authentication Protection
require_once __DIR__ . '/../Includes/Admin_auth.php';

//Load Admin class
require_once __DIR__ . '/../classes/Admin.php';

//Load User class
require_once __DIR__ . '/../classes/User.php';

$adminModel = new Admin();
$userModel  = new User();
 
$admin         = $userModel->findById($_SESSION['user_id']);
$first         = $admin['first'] ?? 'Admin';
 
$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error']   ?? '';
unset($_SESSION['success'], $_SESSION['error']);
 
$students        = $adminModel->getAllStudentsWithAdvisors();
$advisors        = $adminModel->getUsersByRole('advisor');
$totalUnassigned = $adminModel->getTotalUnassignedStudents();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Advisor – Admin Panel</title>
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
            <h2>🔗 Assign Advisor to Students</h2>
            <p>Assign each student to an advisor. The advisor will have full access to that
               student's profile, courses, and assignments — and can grade them.</p>
        </div>
 
        <!-- Stats -->
        <div class="dashboard-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">

            <div class="stat-card">
                <div class="stat-icon">🎓</div>
                <div>
                    <div class="stat-value"><?php echo count($students); ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">⚠️</div>
                <div>
                    <div class="stat-value"><?php echo $totalUnassigned; ?></div>
                    <div class="stat-label">Not Yet Assigned</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🧑‍🏫</div>
                <div>
                    <div class="stat-value"><?php echo count($advisors); ?></div>
                    <div class="stat-label">Available Advisors</div>
                </div>
            </div>

        </div>
 
        <?php if (empty($advisors)) : ?>
            <div class="alert alert-warning">
                ⚠️ No advisors exist yet.
                <a href="Create_Advisor.php" style="font-weight:700; margin-left:6px;">
                    Create an advisor first →
                </a>
            </div>
        <?php endif; ?>
 
        <div class="profile-section" style="padding:0; overflow:hidden;">

            <div style="padding:16px 20px; border-bottom:1px solid var(--border);">
                <h3 style="margin:0;">All Students & Their Assigned Advisors</h3>
            </div>
 
            <?php if (!empty($students)) : ?>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Current Advisor</th>
                            <th>Assign / Change</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($students as $i => $s) : ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($s['first'] . ' ' . $s['last']); ?></strong><br>
                                <small style="color:var(--text-muted);">
                                    STU-<?php echo str_pad($s['id'], 5, '0', STR_PAD_LEFT); ?>
                                </small>
                            </td>
                            <td><?php echo htmlspecialchars($s['email']); ?></td>
                            <td>
                                <?php if (!empty($s['advisor_first'])) : ?>
                                    <span class="badge badge-advisor">
                                        <?php echo htmlspecialchars($s['advisor_first'] . ' ' . $s['advisor_last']); ?>
                                    </span>
                                <?php else : ?>
                                    <span class="badge badge-pending">Not Assigned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($advisors)) : ?>
                                <form method="post" action="../actions/Admin_Actions/Admin_AssignAdvisor.php"
                                      style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                                    <input type="hidden" name="action"     value="assign_advisor">
                                    <input type="hidden" name="student_id" value="<?php echo (int)$s['id']; ?>">
                                    <select name="advisor_id"
                                            style="padding:5px 8px; border-radius:4px; font-size:0.82rem;
                                                   border:1px solid var(--border); margin-bottom:0; width:auto;">
                                        <option value="">-- Select Advisor --</option>
                                        <?php foreach ($advisors as $adv) : ?>
                                            <option value="<?php echo (int)$adv['id']; ?>"
                                                <?php echo ((int)($s['advisor_id'] ?? 0) === (int)$adv['id']) ? 'selected' : ''; ?>>
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
                                <?php if (!empty($s['advisor_first'])) : ?>
                                <form method="post" action="../actions/Admin_Actions/Admin_AssignAdvisor.php"
                                      style="display:inline;"
                                      onsubmit="return confirm('Remove advisor from <?php echo htmlspecialchars($s['first']); ?>?');">
                                    <input type="hidden" name="action"     value="unassign_student">
                                    <input type="hidden" name="student_id" value="<?php echo (int)$s['id']; ?>">
                                    <button type="submit" class="tbl-btn tbl-btn-delete">✖ Remove</button>
                                </form>
                                <?php else : ?>
                                    <span style="color:var(--text-muted);">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else : ?>
                <p class="empty-state">📭 No students registered yet.</p>
            <?php endif; ?>

        </div>
 
    </main>
    
</div>
 
<footer><p>&copy; 2026 School Portal by Mubarak</p></footer>
</body>
</html>