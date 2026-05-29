<?php
session_start();

//Authentication Protection
require_once __DIR__ . '/../../Includes/Student_auth.php';

//Load User from classes folder
require_once __DIR__. "/../../classes/User.php";

//Authentication Check
if(empty($_SESSION['user_id'])) {
    header("Location: ../../Student/Profile.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== "POST") {
    header("Location: ../../Student/Profile.php");
    exit;
}

$userModel = new User();
$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

//Upload Image
if($action === 'upload_photo') {

    if(!isset($_FILES['photo']) || $_FILES['photo']['error'] !==0) {
        $_SESSION['error'] = "Upload Failed";
        header("Location: ../../Student/Profile.php");
        exit;
    }

    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    //Check file type
    if(!in_array($ext, $allowed)) {
        $_SESSION['error'] = "Invalid file type. Only jpeg, jpg, png, gif and webp are allowed";
        header("Location: ../../Student/Profile.php");
        exit;
    }

    //Check file size
    if($_FILES['photo']['size'] > 2000000) {
        $_SESSION['error'] = "Photo is too large";
        header("Location: ../../Student/Profile.php");
        exit;
    }

    $filename = "user_{$user_id}_" . time(). "." . $ext;
    $destination = __DIR__. "/../../uploads/Student_images/" .$filename;

    if(move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
        $userModel->updateProfileImage($user_id, $filename);
        $_SESSION['success'] = "Photo Uploaded.";
    } else {
        $_SESSION['error'] = "Upload failed.";
    }

    header("Location: ../../Student/Profile.php");
    exit;

}

//Update Name
if($action === 'update_name') {

    $first = trim($_POST['first'] ?? '');
    $last  = trim($_POST['last'] ?? '');

    if(empty($first) || empty($last)) {
        $_SESSION['error'] = "First and last name are required."; 
    } else {
        $userModel->updateName($user_id, $first, $last);
        $_SESSION['success'] = "Name Updated Successfully";
    }

    header("Location: ../../Student/Profile.php");
    exit;

}

//Change Password
if($action === "change_password") {

    $current = $_POST['current_pwd'] ?? '';
    $new     = $_POST['new_pwd']     ?? '';
    $confirm = $_POST['confirm_pwd'] ?? '';

    if($new !== $confirm) {
        $_SESSION['error'] = "Password do not match";
    } else {
        if(!$userModel->updateVerifyPassword($user_id, $current)) {
            $_SESSION['error'] = "Current password incorrect";
        } else {
            $userModel->updatePassword($user_id, $new); 
            $_SESSION['success'] = "Password updated";
        }
    }

    header("Location: ../../Student/Profile.php");
    exit;

}


?>