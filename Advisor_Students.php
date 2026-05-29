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
 
$search = trim($_GET['search'] ?? '');
$students = !empty($search)
    ? $advisorModel->searchMyStudents($advisor_id, $search)
    : $advisorModel->getMyStudents($advisor_id);
 
$total = count($students);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Students – Advisor Panel</title>
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
            <h2>🎓 My Students</h2>
            <p>All students assigned to you. Click View to see their full profile, courses and assignments.</p>
        </div>
 
        <!-- Search -->
        <div class="profile-section">

            <form method="GET" action="Advisor_Students.php">

                <div class="filter-bar">
                    <input type="text" name="search" placeholder="🔍 Search by name or email..." 
                    value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="submit-btn" style="margin:0;">Search</button>
                    <a href="Advisor_Students.php" class="cancel-btn" style="padding:10px 22px;">Clear</a>
                </div>

            </form>

        </div>
 
        <div class="profile-section" style="padding:0; overflow:hidden;">

            <div style="padding:16px 20px; border-bottom:1px solid var(--border);">
                <h3 style="margin:0;">
                    <?php echo $total; ?> Student<?php echo $total !== 1 ? 's' : ''; ?> Found
                </h3>
            </div>
 
            <?php if (!empty($students)) : ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Student ID</th>
                            <th>Assigned</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($students as $i => $s) : ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td>
                                <?php
                                $img = __DIR__ . '/../uploads/Advisor_images/' . ($s['profile_image'] ?? '');
                                if (!empty($s['profile_image']) && is_file($img)) : ?>
                                    <img src="../uploads/Advisor_images/<?php echo htmlspecialchars($s['profile_image']); ?>"
                                         style="width:38px;height:38px;border-radius:50%;object-fit:cover;
                                                border:2px solid var(--primary);">
                                <?php else : ?>
                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%23ddd' width='100' height='100'/%3E%3Ctext x='50%25' y='50%25' font-size='50' fill='%23999' text-anchor='middle' dy='.3em'%3EU%3C/text%3E%3C/svg%3E"
                                         style="width:38px;height:38px;border-radius:50%;">
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($s['first'] . ' ' . $s['last']); ?></strong></td>
                            <td><?php echo htmlspecialchars($s['email']); ?></td>
                            <td>STU-<?php echo str_pad($s['id'], 5, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo date('M j, Y', strtotime($s['assigned_at'])); ?></td>
                            <td>
                                <a href="Student_Details.php?id=<?php echo (int)$s['id']; ?>"
                                   class="tbl-btn tbl-btn-view">👁 View Profile</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p class="empty-state">📭 No students found.</p>
            <?php endif; ?>

        </div>
 
    </main>

</div>
 
<footer><p>&copy; 2026 School Portal by Mubarak</p></footer>

</body>
</html>