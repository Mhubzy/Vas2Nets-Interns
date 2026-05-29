<?php
//Authentication Protection
require_once __DIR__ ."/../Includes/Student_auth.php";

//Load User Class
require_once __DIR__ . '/../classes/User.php';

//Load Student Class
require_once __DIR__ . '/../classes/Student.php';


$message = '';

// Error Message
if (isset($_SESSION['success'])){
    $message = ['type' => 'success', 'text' => $_SESSION['success']]; 
    unset($_SESSION['success']);
}

// Success Message
if(isset($_SESSION['error'])) {
    $message = ['type' => 'error', 'text' => $_SESSION['error']];
    unset($_SESSION['error']); 
}

$userModel    = new User();
$studentModel = new Student();
$user = $userModel->findByEmail($_SESSION['email']);

$first          = $user['first']            ?? '';
$last           = $user['last']             ?? '';
$email          = $user['email']            ?? '';
$profile_image  = $user['profile_image']    ?? '';
$id             = $user['id']               ?? null;

//Logged in student_id before using it
$student_id = $_SESSION['user_id'];

$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error']   ?? '';
unset($_SESSION['success'], $_SESSION['error']);

//Only show courses the student is enrolled in
$courses = $studentModel->getEnrolledCourses($student_id);

//Show student's past submission
$submission = $studentModel->getStudentSubmissions($student_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments</title>
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

            <main class="main-content">

                <h2>📚Assignments</h2>
                <p>Welcome back, <?php echo htmlspecialchars($first); ?>! Here you can submit your assignments.</p>
        
                <div class="profile-image">
                    <?php
                        $image_path = __DIR__ . '/../uploads/Student_images/' . $profile_image;
                        if (!empty($profile_image) && is_file($image_path)) : ?>
                        <img src="../uploads/Student_images/<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Picture">
                    <?php else : ?>

                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%23ddd' width='100' height='100'/%3E%3Ctext x='50%25' y='50%25' font-size='50' fill='%23999' text-anchor='middle' dy='.3em'%3EUser%3C/text%3E%3C/svg%3E" alt="Default Avatar">
                    <?php endif; ?>
                </div>

                <?php if($message): ?> 
                    <div class="alert alert-<?php echo $message['type']; ?>">
                        <?php echo htmlspecialchars($message['text']); ?>
                    </div>
                <?php endif; ?>
        
                <div class="user-info">
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($first . ' ' . $last); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>Student ID:</strong> STU-<?php echo str_pad($id ?? 0, 5, '0', STR_PAD_LEFT); ?></p>
                </div>

                <!-- Submit assignment form -->
                <div class="profile-section">

                    <h3>✏️ Submit Assignment</h3>
                    <?php if(empty($courses)): ?>
                        <div class="alert alert-warning">
                            ⚠️ You are not enrolled in any courses yet.
                            <a href="Course.php" style="font-weight:700; margin-left:6px;">Register for a course first →</a>
                        </div>
                    <?php else : ?>
                        <form method="post" action="../actions/Student_Actions/Submit_Assignments.php" enctype="multipart/form-data">  
                            
                            <input type="hidden" name="action" value="submit_assignment">

                               <label for="course_id">Select Course *</label>
                       
                                <select name="course_id" required>

                                    <option value="">-- Select Course --</option>

                                    <?php foreach($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>">
                                            <?php echo htmlspecialchars($course['course_title']); ?>
                                        </option>                            
                                    <?php endforeach; ?>

                                </select>
                        
                            <input type="file" name="file"required>

                            <button type="submit" class="submit-btn">💾 Submit Assignments</button>

                        </form>
                    <?php endif; ?>

                </div>
                
                <!-- Past Submissions -->
                <div class="profile-section" style="padding:0; overflow:hidden;">

                    <h2 class="section-title" style="margin-top:20px;">📋 My Submissions</h2>

                    <?php if (!empty($submissions)) : ?>
                        <table class="data-table">
                            
                            <thead>
                            
                                <tr>
                                    <th>#</th>
                                    <th>Course</th>
                                    <th>File</th>
                                    <th>Submitted</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($submissions as $i => $s) : ?>
                                    
                                    <tr>
                                        <td><?php echo $i + 1; ?></td>
                                        <td><?php echo htmlspecialchars($s['course_title']); ?></td>
                                        <td> <a href="../uploads/Assignment_files/<?php echo htmlspecialchars($s['filename']); ?>"
                                        target="_blank" class="tbl-btn tbl-btn-view">📄 View</a></td>
                                        <td><?php echo date('M j, Y', strtotime($s['submitted_at'])); ?></td>
                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                    </table>

                    <?php else : ?>
                        <p class="empty-state">📭 No assignments submitted yet.</p>
                    <?php endif; ?>

                </div>
        
            </main>

        </div>

    </body>
</html>