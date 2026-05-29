<?php
//Authentication Protection
require_once __DIR__ . '/../../Includes/Admin_auth.php';

//Load Admin class
require_once __DIR__ . '/../../classes/Admin.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../Admin/Create_Courses.php");
    exit;
}

$adminModel   = new Admin();
$course_title = trim($_POST['course_title'] ?? '');
$course_code  = trim($_POST['course_code']  ?? '');
$description  = trim($_POST['description']  ?? '');
$action       = $_POST['action']            ?? '';
$errors = [];


// If validation fails, redirect back
if (!empty($errors)) {

    $_SESSION['errors'] = $errors;

    $_SESSION['old'] = [
        'course_title' => $course_title,
        'course_code'  => $course_code,
    ];

    header('Location: ../../Admin/Create_Advisor.php');
    exit();
}


//Create Course 
$result = $adminModel->createCourse($course_title, $course_code, $description);

if($result['success']) {

    $_SESSION['success'] = [
        "Course created successfully. You can now assign advisor to this course."
    ];

} else {

    $_SESSION['errors'] = [
        "Course creation failed. Please try again later"
    ];
    
}

header("Location: ../../Admin/Create_Courses.php");
exit;

?>

