<?php
//Authentication Protection
require_once __DIR__ . '/../Includes/Admin_auth.php';

//Load User class
require_once __DIR__ . '/../classes/User.php';
 
$userModel = new User();
$admin     = $userModel->findById($_SESSION['user_id']);
$first     = $admin['first'] ?? 'Admin';
 
$success = $_SESSION['success']  ?? [];
$errors   = $_SESSION['errors']  ?? [];
$old     = $_SESSION['old']      ?? [];

unset($_SESSION['success'], $_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Advisor – Admin Panel</title>
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
 
        <?php if (!empty($success)) : ?>
            <div class="alert alert-success">
                <ul>
                    <?php foreach ($success as $message) : ?>
                        <li><?= htmlspecialchars($message) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)) : ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><p><?= htmlspecialchars($error) ?></p></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
 
        <div class="admin-banner">
            <h2>➕ Create New Advisor</h2>
            <p>Create a new advisor account. Once created, go to Assign Advisor to assign students to them.</p>
        </div>
 
        <div class="profile-section">

            <h3>🧑‍🏫 Advisor Account Details</h3>
            
            <form method="POST" action="../actions/Admin_Actions/Create_Advisor.php">
 
                <label for="first">First Name *</label>
                <input type="text" name="first" id="first" value="<?php echo htmlspecialchars($old['first'] ?? ''); ?>" 
                placeholder="First Name" required>
 
                <label for="last">Last Name *</label>
                <input type="text" name="last" id="last" value="<?php echo htmlspecialchars($old['last'] ?? ''); ?>"
                placeholder="Last Name" required>
 
                <label for="email">Email Address *</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>"
                placeholder="email" required>
 
                <label for="password">Password * <small>(min. 6 characters)</small></label>
                <input type="password" name="password" id="password" placeholder="password">
 
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="confirm_password">
 
                <div class="form-buttons">
                    <button type="submit" class="submit-btn">✅ Create Advisor</button>
                    <a href="Assign_Advisor.php" class="cancel-btn">✖ Cancel</a>
                </div>
 
            </form>

        </div>
 
    </main>

</div>
 
<footer><p>&copy; 2026 School Portal by Mubarak</p></footer>

</body>
</html>