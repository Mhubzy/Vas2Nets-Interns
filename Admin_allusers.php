<?php
session_start();

require_once __DIR__ . "/../../Includes/Admin_auth.php";
require_once __DIR__ . "/../../classes/Admin.php"; 

//Only accept POST
if($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../Admin/All_Users.php");
    exit;
}

$adminModel = new Admin();
$action = $_POST['action'] ?? '';

//____ Change User Role ______________
if($action === "change_role") {

    $user_id = (int)($_POST['user_id'] ?? 0);
    $role    = trim($_POST['role'] ?? '');

    //Prevent admin from changing his own role
    if($user_id === (int) $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot change your own role";
        header("Location: ../../Admin/All_Users.php");
        exit;
    }

    if($user_id <=0 || empty($role)) {
        $_SESSION['error'] = "Invalid request. Please try again";
        header("Location: ../../Admin/All_Users.php");
        exit;
    }

    $result = $adminModel->updateUserRole($user_id, $role);

    if($result['success']) {
        $_SESSION['success'] = "User role updated to " .ucfirst($role). " successfully.";
    } else {
        $_SESSION['error'] = "Unable to update user role. Please try again later.";
    } 

    header("Location: ../../Admin/All_Users.php");
    exit;

}

//________Delete User________________
if($action === 'delete_user') {

    $user_id =  (int)($_POST['user_id'] ?? 0);

    //Prevent admin from deleting themselves
    if($user_id === (int) $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot delete your own account";
        header("Location: ../../Admin/All_Users.php");
        exit;
    }

    if($user_id <= 0) {
        $_SESSION['error'] = "Invalid request. Please try again";
        header("Location: ../../Admin/All_Users.php");
        exit;
    }

    $result = $adminModel->deleteUser($user_id);

    if($result['success']) {
        $_SESSION['success'] = "User deleted successfully";
    } else {
        $_SESSION['error'] = "Unable to delete user. Please try again later.";
    }

    header("Location: ../../Admin/All_Users.php");
    exit;

}

// Fallback — unknown action
header("Location: ../../Admin/All_Users.php");
exit;

?>