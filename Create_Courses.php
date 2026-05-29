<?php

//Authentication Protection
require_once __DIR__ . '/../Includes/Admin_auth.php';

//Load Admin class
require_once __DIR__ . '/../classes/Admin.php';

//Load User class
require_once __DIR__ . '/../classes/User.php';

$adminModel = new Admin();
$userModel  = new User();
 
$admin     = $userModel->findById($_SESSION['user_id']);
 
$success = $_SESSION['success'] ?? [];
$errors  = $_SESSION['errors']  ?? [];
$old     = $_SESSION['old']     ?? [];

unset($_SESSION['success'], $_SESSION['errors'], $_SESSION['old']);

$advisors        = $adminModel->getUsersByRole('advisor');
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
            <h2>➕ Create New Courses</h2>
            <p>Create a new course.</p>
        </div>
 
        <div class="profile-section">

            <h3>🧑‍🏫 New Course Details</h3>
            
            <form method="POST" action="../actions/Admin_Actions/Create_Course.php">
 
                <label for="course_title">Course Title *</label>
                <input type="text" name="course_title" id="course_title" value="<?php echo htmlspecialchars($old['course_title'] ?? ''); ?>" 
                placeholder="Course Title" required>
 
                <label for="course_code">Course Code *</label>
                <input type="text" name="course_code" id="course_code" value="<?php echo htmlspecialchars($old['course_code'] ?? ''); ?>"
                placeholder="Course Code" required>
 
                <label for="description">Description</label>
                <textarea name="description" id="description" placeholder="description" >
                    <?php echo htmlspecialchars($old['description'] ?? ''); ?>
                </textarea>

                <div class="form-buttons">
                    <button type="submit" class="submit-btn">✅ Create Course</button>
                    <a href="Assign_Courses.php" class="cancel-btn">✖ Cancel</a>
                </div>
 
            </form>

        </div>
 
    </main>

</div>
 
<footer><p>&copy; 2026 School Portal by Mubarak</p></footer>

</body>
</html>