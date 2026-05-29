<?php
session_start();

//Authentication Protection
require_once __DIR__ . '/../../Includes/Admin_auth.php';

//Load Admin class
require_once __DIR__ . '/../../classes/Admin.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../Admin/Assign_Courses.php');
    exit();
}   

$adminModel = new Admin();
$action = $_POST['action'] ?? '';    

//Assign course to advisor
if($action === 'assign_course') {

    $course_id  = (int) ($_POST['course_id'] ?? 0);
    $advisor_id = (int) ($_POST['advisor_id'] ?? 0);

    $result = $adminModel->assignCourseToAdvisor($course_id, $advisor_id);

    if($result['success']) {
        $_SESSION['success'] = 'Course assigned to advisor successfully';
    } else {
        $_SESSION['error'] = "Unable to assign course to advisor. Please try again later.";
        header("Location: ../../Admin/Assign_Courses.php");
        exit;
    }

}


//_____Remove advisor from a course____________
if($action === "unassign_course") {

    $course_id = (int) ($_POST['course_id'] ?? 0);

    if($course_id <= 0) {
        $_SESSION['error'] = "Invalid request. Please try again";
        header("Location: ../../Admin/Assign_Courses.php");
        exit;
    }

    $result = $adminModel->unassignCourse($course_id);

    if($result['success']) {
        $_SESSION['success'] = "Advisor removed from course successful";
    } else{
        $_SESSION['error'] = "Unable to remove advisor from course. Please try again later.";
    }

    header("Location: ../../Admin/Assign_Courses.php");
    exit;

}

//__________Delete a course permanently___________
if($action === "delete_course") {

    $course_id = (int) ($_POST['course_id'] ?? 0);

    if($course_id <=0 ) {
        $_SESSION['error'] = "Invalid request. Please try again later.";
        header("Location: ../../Admin/Assign_Courses.php");
        exit;
    }

    $result = $adminModel->deleteCourse($course_id);

    if($result['success']) {
        $_SESSION['success'] = "Course deleted permanently.";
    } else {
        $_SESSION['error'] = "Unable to delete course permanently. Please try again later.";
    }

    header("Location: ../../Admin/Assign_Courses.php");
    exit;
}

//fallback - unknown action
header("Location: ../../Admin/Assign_Courses.php");
exit;


?>
