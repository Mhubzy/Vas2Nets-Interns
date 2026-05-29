<?php
session_start();

//Authentication Protection
require_once __DIR__ . '/../../Includes/Admin_auth.php';

//Load Admin class
require_once __DIR__ . '/../../classes/Admin.php';

//Only accept POST
if($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../Admin/All_Assignments.php");
    exit;
}

$adminModel = new Admin();
$action = $_POST['action'] ?? '';

//_________Delete Assignments_____________
if($action === 'delete_assignment') {

    $assignment_id = (int) ($_POST['assignment_id'] ?? 0);
 
    if($assignment_id <= 0 ) {
        $_SESSION['error'] = "Invalid request. Please try again";
        header("Location: ../../Admin/All_Assignments.php");
        exit();
    }

    $result = $adminModel->deleteAssignment($assignment_id);

    if($result['success']) { 
        $_SESSION['success'] = "Assignment deleted successfully.";
    } else {
        $_SESSION['error'] = "Assignment delete failed. Please try again later.";
    }

    header("Location: ../../Admin/All_Assignments.php");
    exit;

}

//Fallback - unknown action
header("Location: ../../Admin/All_Assignments.php");
exit;

?>
