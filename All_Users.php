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

//Search Filter
$search      = trim($_GET['search'] ?? '');
$role_filter = trim($_GET['role']   ?? '');

if(!empty($search)) {
    $users = $adminModel->searchUsers($search);
} elseif (!empty($role_filter)) {
    $users = $adminModel->getUsersByRole($role_filter);
} else {
    $users = $adminModel->getAllUsers();
}

$totalUsers = count($users);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users – Admin Panel</title>
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
            <h2>👥 All Users</h2>
            <p>Manage all registered users — view, change roles or remove accounts.</p>
        </div>
 
        <!-- Search & Filter Bar -->
        <div class="profile-section">

            <form method="GET" action="All_Users.php">

                <div class="filter-bar">
                    <input type="text" name="search" placeholder="🔍 Search by name or email..."
                    value="<?php echo htmlspecialchars($search); ?>">

                    <select name="role">
                        <option value="">All Roles</option>
                        <option value="student"  <?php echo $role_filter === 'student'  ? 'selected' : ''; ?>>Students</option>
                        <option value="advisor"  <?php echo $role_filter === 'advisor'  ? 'selected' : ''; ?>>Advisors</option>
                        <option value="admin"    <?php echo $role_filter === 'admin'    ? 'selected' : ''; ?>>Admins</option>
                    </select>
                    
                    <button type="submit" class="submit-btn" style="margin:0;">Filter</button>
                    <a href="All_Users.php" class="cancel-btn" style="padding:10px 22px;">Clear</a>
                </div>

            </form>

        </div>
 
        <!-- Users Table -->
        <div class="profile-section" style="padding:0; overflow:hidden;">
 
            <div style="padding:16px 20px; border-bottom:1px solid var(--border);
                        display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0;">
                    <?php echo $totalUsers; ?> User<?php echo $totalUsers !== 1 ? 's' : ''; ?> Found
                </h3>
            </div>
 
            <?php if (!empty($users)) : ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $i => $u) : ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo htmlspecialchars($u['first'] . ' ' . $u['last']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo htmlspecialchars($u['role']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($u['role'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
                            <td>
                                <div style="display:flex; gap:6px; flex-wrap:wrap;">
 
                                    <!-- Change Role Form -->
                                    <form method="post" action="../actions/Admin_Actions/Admin_allusers.php"
                                          style="display:inline;">
                                        <input type="hidden" name="action" value="change_role">
                                        <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                                        <select name="role" style="padding:5px 8px; border-radius:4px;
                                                font-size:0.8rem; border:1px solid var(--border);
                                                margin-bottom:0; width:auto;">
                                            <option value="student"  <?php echo $u['role'] === 'student'  ? 'selected' : ''; ?>>Student</option>
                                            <option value="advisor"  <?php echo $u['role'] === 'advisor'  ? 'selected' : ''; ?>>Advisor</option>
                                            <option value="admin"    <?php echo $u['role'] === 'admin'    ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                        <button type="submit" class="tbl-btn tbl-btn-edit"
                                                style="margin-left:4px;">Save</button>
                                    </form>
 
                                    <!-- Delete Button -->
                                    <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']) : ?>
                                        <form method="post" action="../actions/Admin_Actions/Admin_allusers.php"
                                              style="display:inline;"
                                              onsubmit="return confirm('Delete <?php echo htmlspecialchars($u['first']); ?>? This cannot be undone.');">
                                            <input type="hidden" name="action"  value="delete_user">
                                            <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                                            <button type="submit" class="tbl-btn tbl-btn-delete">🗑 Delete</button>
                                        </form>
                                    <?php else : ?>
                                        <span style="font-size:0.78rem; color:var(--text-muted);">(You)</span>
                                    <?php endif; ?>
 
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p class="empty-state">📭 No users found.</p>
            <?php endif; ?>
        </div>
 
    </main>
</div>
 
<footer><p>&copy; 2026 School Portal by Mubarak</p></footer>
 
</body>
</html>