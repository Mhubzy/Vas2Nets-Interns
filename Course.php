<?php
//Authentication Protection
require_once __DIR__ ."/../Includes/Student_auth.php";

//Load User class
require_once __DIR__ . '/../classes/User.php';

//Load Student Class
require_once __DIR__ . '/../classes/Student.php';

$userModel    = new User();
$studentModel = new Student();
$user      = $userModel->findByEmail($_SESSION['email']);

$first          = $user['first']            ?? '';
$last           = $user['last']             ?? '';
$email          = $user['email']            ?? '';
$profile_image  = $user['profile_image']    ?? '';
$id             = $user['id']               ?? null;

//Logged in students
$student_id     = $_SESSION['user_id'];

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error']   ?? '';
unset($_SESSION['success'], $_SESSION['error']);

//Courses assigned to this student advisors
$advisorCourses = $studentModel->getCoursesForStudent($student_id);

//Courses the student is already enrolled in
$enrolledCourses   = $studentModel->getEnrolledCourses($student_id);
$enrolledCourseIds = array_column($enrolledCourses, 'id');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses</title>
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

        <h2>📚 Available Courses</h2>

        <!-- Error Message -->
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color:red">
                <?= $_SESSION['error']; unset($_SESSION['error']);?> 
            </p>
        <?php endif; ?>

        <!-- Success Message -->
         <?php if(isset($_SESSION['success'])): ?>
            <p style="color:green">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </p>
        <?php endif; ?>

        <p>Welcome back, <?php echo htmlspecialchars($first); ?>! Here you can manage your courses.</p>

        <div class="profile-image">
            <?php
            $image_path = __DIR__ . '/../uploads/Student_images/' . $profile_image;
            if (!empty($profile_image) && is_file($image_path)) : ?>
                <img src="../uploads/Student_images/<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Picture">
            <?php else : ?>
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%23ddd' width='100' height='100'/%3E%3Ctext x='50%25' y='50%25' font-size='50' fill='%23999' text-anchor='middle' dy='.3em'%3EUser%3C/text%3E%3C/svg%3E" alt="Default Avatar">
            <?php endif; ?>
        </div>
        
        
        <div class="user-info">
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($first . ' ' . $last); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Student ID:</strong> STU-<?php echo str_pad($id ?? 0, 5, '0', STR_PAD_LEFT); ?></p>
        </div>

        <?php if (empty($advisorCourses)) : ?>
            <div class="alert alert-warning">
            ⚠️ No courses are available yet. You have not been assigned to an advisor, or your advisor has no courses.
            </div>
        <?php else : ?>
            <div class="dashboard-grid">

                <?php foreach($advisorCourses as $course): ?>
 
                    <div class="card">

                        <h3><?php echo htmlspecialchars($course['course_title'])?></h3>
                        <p><strong>Code:</strong><?php echo htmlspecialchars($course['course_code'])?></p>
                        <p><?php htmlspecialchars($course['description'])?></p>
                        <p><strong>Advisor:</strong><?php echo htmlspecialchars($course['first'] . ' ' .$course['last'])?></p>

                        <?php if(in_array($course['id'], $enrolledCourseIds)): ?>

                            <!-- Already enrolled. show Drop button -->
                            <p style="color: green font-weight 600;">✔ Enrolled</p>
                            <form method="POST" action="../actions/Student_Actions/Register_Course.php" 
                            onsubmit="return confirm('Drop <?php echo htmlspecialchars($course['course_title']); ?> ?');">
                                <input type="hidden" name="action" value="drop_course">
                                <input type="hidden" name="course_id" value="<?php echo (int) $course['id']; ?>">
                                <button type="submit" class="tbl-btn tbl-btn-delete">🗑 Drop Course</button>
                            </form>

                        <?php else: ?>

                            <!-- Not enrolled - Show registration button -->
                            <form action="../actions/Student_Actions/Register_Course.php" method="POST">
                                <input type="hidden" name="action" value="register_course">
                                <input type="hidden" name="course_id" value="<?php echo (int)$course['id']; ?>">
                                <button type="submit" class="submit-btn"> ➕ Register</button>
                            </form>

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </main>
    
</div>

<footer><p>&copy; 2026 School Portal by Mubarak</p></footer>

<script>
// toggleForm adds/removes ?show_form=1 from the URL to show/hide the add form
function toggleForm() {
    const url = new URL(window.location);
    if (url.searchParams.has('show_form')) {
        url.searchParams.delete('show_form');
    } else {
        url.searchParams.set('show_form', '1');
    }
    window.location.href = url.toString();
}
</script>
</body>
</html>