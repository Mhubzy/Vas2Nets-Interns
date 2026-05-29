<?php

//Authentication Protection
require_once __DIR__ . '/../../Includes/Student_auth.php';

//Load User class
require_once __DIR__ . '/../../classes/User.php';

//Load Student Class
require_once __DIR__ . '/../../classes/Student.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../Student/Course.php");
    exit;
}

$userModel  = new User();
$studentModel    = new Student();
$student_id = $_SESSION['user_id'];
$course_id  = (int) ($_POST['course_id'] ?? 0);
$action     = $_POST['action'] ?? '';

//Check if the course have been registered
if($course_id <=0 ) {
    $_SESSION['error'] = "Invalid Course";
    header("Location: ../../Student/Course.php");
    exit;
}

//Register for a course 
if($action === 'register_course') {

    $result = $studentModel->enrollCourse($student_id, $course_id);

    if($result['success']) {
        $_SESSION['success'] = "Course registered successfully";
    } else {
        $_SESSION['error'] = "Course registration failed. Please try again later";
    }

    header("Location: ../../Student/Course.php");
    exit;
}

//Drop a course
if($action === 'drop_course') {
    
    $result = $studentModel->dropCourse($student_id, $course_id);

    if($result['success']) {
        $_SESSION['success'] = "Course dropped successfully";
    } else {
        $_SESSION['error'] = "Course drop failed. Please try again later.";
    }

    header("Location: ../../Student/Course.php");
    exit;
}

header("Location: ../../Student/Course.php");
exit;

?>
