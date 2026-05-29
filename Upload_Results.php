<?php
//Authentication Protection
require_once __DIR__ . '/../Includes/Advisor_auth.php';

//Load Advisor class
require_once __DIR__ . '/../classes/Advisor.php';

//Load User class
require_once __DIR__ . '/../classes/User.php';
 
$advisorModel = new Advisor();
$userModel    = new User();
 
$advisor       = $userModel->findById($_SESSION['user_id']);
$advisor_id    = (int) $_SESSION['user_id'];
$first         = $advisor['first']         ?? 'Advisor';
$last          = $advisor['last']          ?? '';
$profile_image = $advisor['profile_image'] ?? '';
 

//Recent activity
$students          = $advisorModel->getMyStudents($advisor_id);
$courses           = $advisorModel->getMyCourses($advisor_id);
 
$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error']   ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Results – School Portal</title>
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
 
        <!-- Welcome Banner -->
        <div class="advisor-banner">

            <div style="display:flex; align-items:center; gap:16px;">
                <div class="profile-image" style="margin:0;">
                    <?php
                    $img = __DIR__ . '/../uploads/Advisor_images/' . $profile_image;
                    if (!empty($profile_image) && is_file($img)) : ?>
                        <img src="../uploads/Advisor_images/<?php echo htmlspecialchars($profile_image); ?>"
                             style="width:60px;height:60px;" alt="Profile">
                    <?php else : ?>
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%23ddd' width='100' height='100'/%3E%3Ctext x='50%25' y='50%25' font-size='50' fill='%23999' text-anchor='middle' dy='.3em'%3EAD%3C/text%3E%3C/svg%3E"
                             style="width:60px;height:60px;" alt="Default">
                    <?php endif; ?>
                </div>
                <div>
                    <h2>Welcome back, <?php echo htmlspecialchars($first); ?>! 👋</h2>
                    <p>Here is an overview of your students — <?php echo date('l, F j, Y'); ?></p>
                </div>
            </div>

        </div>

        <!-- Upload Results Form -->
        <h2 class="section-title" style="margin-top:10px;">Upload Students Result</h2>
        <form method="POST" action="../actions/Advisor_Actions/Upload_Results.php">

            <label for="students">Students</label><br>
            <select name="student_id" required>
                <option value="">Select Students</option>

                <?php foreach ($students as $student): ?>
                    <option value="<?php echo $student['id']; ?>">
                        <?php echo htmlspecialchars($student['first']. ' ' . $student['last']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
         
            <label for="courses">Courses</label><br>
            <select name="course_id" required>
                <option value="">Select Course</option>

                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['id']; ?>">
                        <?php echo htmlspecialchars($course['course_title']); ?>
                    </option>
                <?php endforeach; ?>
            </select> 
            
            <br><br>

            <label for="score">Score</label><br>
            <input type="number" name="score" min= "0" max= "100" required>

            <br><br>
        
            <label for="grade_letter">Grade</label><br>
            <input type="text" name="grade_letter" required>

            <br><br>

            <label for="comment">Comment</label>
            <textarea name="comment"></textarea>

            <br><br>

            <button type="submit">Upload Results</button>

        </form>

    </main>

</div>

</body>
</html>