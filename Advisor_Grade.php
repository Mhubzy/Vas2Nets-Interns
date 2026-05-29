<?php
//Authentication Protection
require_once __DIR__ . '/../../Includes/Advisor_auth.php';

//Load Advisor class
require_once __DIR__ . '/../../classes/Advisor.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../Advisor/Advisor_Assignments.php');
    exit(); 
}

$advisorModel  = new Advisor();
$advisor_id    = $_SESSION['user_id'];
$assignment_id = (int) ($_POST['assignment_id'] ?? 0);
$score         = trim($_POST['score']           ?? '');
$comment       = trim($_POST['comment']         ?? '');

//Redirect tells us which page to go back to after grading
$redirect_raw = $_POST['redirect'] ?? 'Advisor_Assignments.php';

//Whitelist allowed redirect pages so that users can't send us to malicious pages
$allowed_redirect = ['Advisor_Assignments.php', 'Student_Details.php'];
$redirect = 'Advisor_Assignments.php'; //Default safe fallback
 
foreach($allowed_redirect as $allowed) {
    if(str_starts_with($redirect_raw, $allowed)) {
        $redirect = $redirect_raw;
        break;
    }
}

if($assignment_id <= 0) {
    $_SESSION['error'] = 'Invalid assignment. Please try again.'; 
    header('Location: ../../Advisor/' . $redirect);
    exit();
}

if(empty($score)) {
    $_SESSION['error'] = 'Score/grade is required.';
    header('Location: ../../Advisor/' . $redirect);
    exit(); 
}

$result = $advisorModel->gradeAssignment($advisor_id, $assignment_id, $score, $comment);

if($result['success']) {
    $_SESSION['success'] = "Grade Saved Successfully.";
} else {
    $_SESSION['error'] = $result['error'];
}

header('Location: ../../Advisor/' . $redirect);
exit();

?>