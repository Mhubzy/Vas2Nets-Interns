<?php
session_start();

//Authentication Protection
require_once __DIR__ . '/../../Includes/Admin_auth.php';

//Load Admin class
require_once __DIR__ . '/../../classes/Admin.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../Admin/Assign_Advisor.php');
    exit();
}   

$adminModel = new Admin();
$action = $_POST['action'] ?? '';    

//______Assign Advisor to Student______
if($action === 'assign_advisor') {

    $student_id = (int) ($_POST['student_id'] ?? 0);
    $advisor_id = (int) ($_POST['advisor_id'] ?? 0);

    // Validate inputs
    if(empty($_POST['advisor_id'])) {
        $_SESSION['error'] = 'Please select an advisor from the dropdown.';
        header('Location: ../../Admin/Assign_Advisor.php');
        exit;
    }

    $result = $adminModel->assignStudentToAdvisor($student_id, $advisor_id);

    if($result['success']) {
        $_SESSION['success'] = 'Advisor assigned to student successfully. The advisor now has full access to this student\'s profile and can manage their courses and progress.';
    } else {
        $_SESSION['error'] = "Advisor assigned to student failed. Please try again later.";
    }

    header('Location: ../../Admin/Assign_Advisor.php');
    exit;
}

//_________Remove Advisor from Student________
if($action === 'unassign_student') {

    $student_id = (int)($_POST['student_id'] ?? 0);

    if($student_id <= 0) {
        $_SESSION['error'] = 'Invalid student selection. Please try again.';
        header('Location: ../../Admin/Assign_Advisor.php');
        exit;
    }

    $result = $adminModel->unassignStudent($student_id);

    if($result['success']) {
        $_SESSION['success'] = 'Advisor removed from student successfully. The student is now unassigned and will not have an advisor until one is assigned again.';
    } else {
        $_SESSION['error'] = "Advisor removed from student failed. Please try again later.";
    }

    header('Location: ../../Admin/Assign_Advisor.php');
    exit;
}

header('Location: ../../Admin/Assign_Advisor.php');
exit;


?>
