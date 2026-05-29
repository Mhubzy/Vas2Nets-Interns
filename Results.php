<?php
//Authentication Protection
require_once __DIR__ ."/../Includes/Student_auth.php";

//Load USer class
require_once __DIR__ ."/../classes/User.php";

//Load Student Class
require_once __DIR__ . '/../classes/Student.php';

$userModel    = new User();
$studentModel = new Student();
$user = $userModel->findById($_SESSION['user_id']);

$first          = $user['first']            ?? '';
$last           = $user['last']             ?? '';
$email          = $user['email']            ?? '';
$profile_image  = $user['profile_image']    ?? '';
$id             = $user['id']               ?? 0;

if(!$studentModel->canViewResults()) {
    die('Results have not been released yet. Please check back later.');
}

$results = $studentModel->getResultsForStudent($_SESSION['user_id']);

if(isset($_SESSION['success'])) {
    $message = "<p style = 'color:green'>" .$_SESSION['success'] ."</p>";
    unset($_SESSION['success']);
}

if(isset($_SESSION['error'])) {
    $message = "<p style = 'color:red'>" .$_SESSION['error'] ."</p>";
    unset($_SESSION['error']);
}

// Flash messages
$success = $_SESSION['success'] ?? ''; 
$error   = $_SESSION['error']   ?? ''; 
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - School Portal</title>
    <link rel="stylesheet" href="../style.css"> 
</head>
<body>
<header><h1>SCHOOL PORTAL</h1></header>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <h3>Menu</h3>
        <ul>
            <li><a href="Student_Dashboard.php">📊 Dashboard</a></li>
            <li><a href="Course.php">📚 My Courses</a></li>
            <li><a href="Profile.php">👤 My Profile</a></li>
            <li><a href="Assignments.php">📝 Assignments</a></li>
            <li><a href="Results.php" target="_blank">📊 Results</a></li>
            <li><a href="../Logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <h2>📊 My Results</h2>
        
        <!-- Success / Error message -->
        <?php if ($success) : ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error) : ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Profile Picture + info summary -->
        <div class="profile-image">
            <?php 
            $image_path = __DIR__ . '/../uploads/Student_images/' . $profile_image;
            if (!empty($profile_image) && is_file($image_path)) : 
            ?>
                <img src="../uploads/Student_images/<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Picture">
            <?php else : ?>
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 150 150'%3E%3Crect fill='%23ddd' width='150' height='150'/%3E%3Ctext x='50%25' y='50%25' font-size='40' fill='%23999' text-anchor='middle' dy='.3em'%3EUser%3C/text%3E%3C/svg%3E" alt="Default Avatar">
            <?php endif; ?>
        </div>

        <div class="user-info">
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($first . ' ' . $last); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Student ID:</strong> STU-<?php echo str_pad($id ?? 0, 5, '0', STR_PAD_LEFT); ?></p>
        </div>
        
        <h2 class="section-title" style="margin-top:10px;">My Results</h2>

        <div class="profile-section" style="padding:0; overflow:hidden;">
            
            <table class="data-table">
            
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Score</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php foreach($results as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['course_title']); ?></td>
                            <td><?php echo htmlspecialchars($r['score']); ?></td>
                            <td><?php echo htmlspecialchars($r['grade_letter']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table> <br>
            
            <a href="Print_Result.php"> <button type="button"> 🖨 Print Result </button> </a>
        </div>

    </main>

</div>

</body>
</html>
