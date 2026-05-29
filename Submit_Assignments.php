<?php

//Authentication Protection
require_once __DIR__ . '/../../Includes/Student_auth.php';

//Load User class
require_once __DIR__ . '/../../classes/User.php';

//Load Student Class
require_once __DIR__ . '/../../classes/Student.php';

//Only accept POST
if($_SERVER['REQUEST_METHOD'] !== "POST") {
    header("Location: ../../Student/Assignments.php");
    exit;
}

$userModel    = new User();
$studentModel = new Student();
$student_id   = $_SESSION['user_id'];
$course_id    = (int) ($_POST['course_id'] ?? '');
$action       = $_POST['action'] ?? '';

//Handle result
if($action === "submit_assignment") {

    //Validate course selected
    if($course_id <=0 ) {
    $_SESSION['error'] = 'Please select a course.'; 
    header("Location: ../../Student/Assignments.php");
    exit;
    }
    
    //Validate file upload 
    if(!isset($_FILES['file']) || $_FILES['file']['error'] !==0) {
        $_SESSION['error'] = 'Please select a file to upload';
        header("Location: ../../Student/Assignments.php");
        exit;    
    }
    
    //Validate file type
    $allowed = ['pdf', 'docx', 'pptx',];
    $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
    
    if(!in_array($ext, $allowed)) {
    $_SESSION['error'] = 'Invalid file type. Only pdf, docx, and pptx are allowed';
    header("Location: ../../Student/Assignments.php");
    exit;
    }

    //Check file size
    if($_FILES['file']['size'] > 10000000) {
    $_SESSION['error'] = 'File is too large';    
    header("Location: ../../Student/Assignments.php");
    exit;
    }

    //Get student details
    $student = $userModel->findById($student_id);

    //Create safe student names
    $first         = preg_replace('/[^a-zA-Z0-9_-]/', '_', trim($student['first']));
    $last          = preg_replace('/[^a-zA-Z0-9_-]/', '_', trim($student['last']));
    $course_title  = preg_replace('/[^a-zA-Z0-9_-]/', '_', trim($student['course_title']));


    //Generate filename
    $filename = $first . "_" . $last . $course_title. " assignment" . time() . "." . $ext;
    $destination = __DIR__. "/../../uploads/Assignment_files/" .$filename;

    // Create folder if not exist
    if (!is_dir(dirname($destination))) {
        mkdir(dirname($destination), 0777, true);
    }

    //Move uploaded file to destination 
    if(!move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
    $_SESSION['error'] = 'File upload failed. Please try again.';
    header("Location: ../../Student/Assignments.php");
    exit;
    }

    $result = $studentModel->submitAssignment($student_id, $course_id, $filename);

    if($result['success']) {
        $_SESSION['success'] = "Assignment submitted successfully";
    } else {
        //Delete the uploaded file if the database inserted failed
        if(file_exists($destination)) {
            unlink($destination);
        }
        $_SESSION['error'] = "Assignment was not submitted successfully.";
    }
    
    header("Location: ../../Student/Assignments.php");
    exit;

}

header("Location: ../../Student/Assignments.php");
exit;

?>