<?php
session_start();

require_once __DIR__ . "/../../Includes/Admin_auth.php";
require_once __DIR__ . "/../../classes/User.php"; 

//Only accept POST
if($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../Admin/Admin_Profile.php");
    exit;
}

$userModel = new User();
$user_id   = $_SESSION['user_id'];
$action    = $_POST['action'] ?? '';

//Upload Image
if($action === 'upload_photo') {

    if(!isset($_FILES['photo']) || $_FILES['photo']['error'] !==0) {
        $_SESSION['error'] = "Upload Failed";
        header("Location: ../../Admin/Admin_Profile.php");
        exit;
    }

    $target_dir = __DIR__ . '/../../uploads/Admin_images/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    //Check file type
    if(!in_array($ext, $allowed)) {
        $_SESSION['error'] = "Invalid file type. Only jpeg, jpg, png, gif and webp are allowed";
        header("Location: ../../Admin/Admin_Profile.php");
        exit;
    }

    //Check file size
    if($_FILES['photo']['size'] > 2000000) {
        $_SESSION['error'] = "Photo is too large";
        header("Location: ../../Admin/Admin_Profile.php");
        exit;
    }

    //Safe unique filename
    $filename = "admin_" .$user_id. '_' .time(). "." . $ext;
    $destination = $target_dir .$filename;

    if(move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
        if($userModel->updateProfileImage($user_id, $filename)) {
            $_SESSION['success'] = "Photo Uploaded.";
        } else {
            $_SESSION['error'] = "Photo saved but could not update database.";
        }
    } else {
        $_SESSION['error'] = "Could not save photo. Please try again.";
    }

    header("Location: ../../Admin/Admin_Profile.php");
    exit;

}

//Update Name
if($action === 'update_name') {

    $first = trim($_POST['first'] ?? '');
    $last  = trim($_POST['last']  ?? '');

    if(empty($first) || empty($last)) {
        $_SESSION['error'] = "First and last name are required."; 
        header("Location: ../../Admin/Admin_Profile.php");
        exit;
    } 
    
    if($userModel->updateName($user_id, $first, $last)){
        $_SESSION['success'] = "Name Updated Successfully";
    } else {
        $_SESSION['error'] = "Could not update name. Please try again later.";
    }

    header("Location: ../../Admin/Admin_Profile.php");
    exit;

}

//Change Password
if($action === "change_password") {

    $current = $_POST['current_pwd'] ?? '';
    $new     = $_POST['new_pwd']     ?? '';
    $confirm = $_POST['confirm_pwd'] ?? '';

    if(empty($current) || empty($new) || empty($confirm)) {
        $_SESSION['error'] = "All password fields required.";
        header("Location: ../../Admin/Admin_Profile.php");
        exit;
    }

    if(strlen($new) <6) {
        $_SESSION['error'] = "New password must be at least 6 characters";
        header("Location: ../../Admin/Admin_Profile.php");
        exit;
    }

    if($new !== $confirm) {
        $_SESSION['error'] = "Password do not match";
        header("Location: ../../Admin/Admin_Profile.php");
        exit;
    } 
    
    //Verify current password is correct
    if(!$userModel->updateVerifyPassword($user_id, $current)) {
        $_SESSION['error'] = "Current password incorrect";
        header("Location: ../../Admin/Admin_Profile.php");
        exit;
    } 
    
    if($userModel->updatePassword($user_id, $new)) {
        $_SESSION['success'] = "Password changes successfully";
    } else {
        $_SESSION['error'] = "Could not update password. Please try again";
    }
            
    header("Location: ../../Admin/Admin_Profile.php");
    exit;
    
}

//Fallback - no action

header("Location: ../../Admin/Admin_Profile.php");
exit;

?>
