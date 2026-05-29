<?php
session_start();

require_once __DIR__ . '/../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    header("Location: ../Login.php");
    exit;
}
 
//trim() removes accidental spaces and the "??" prevents undefined index error if a field is missing
$email = trim($_POST['email']   ?? '');  
$pwd   = trim($_POST['pwd']     ?? '');

//Validation
if (empty($email) || empty($pwd)) {
    $_SESSION['error'] =  "Please provide both email and password.";
    $_SESSION['old'] = $_POST;
    header("Location: ../Login.php");
    exit;
} 

//Use User class OOP Layer
$userModel = new User();
$user = $userModel->findByEmail($email);

//Check if user exist
if(!$user) {
    $_SESSION['error'] = "No account found with that email.";
    $_SESSION['old'] = $_POST;
    header("Location: ../Register.php");
    exit;
}

//Verify Password
if(!$userModel->verifyPassword($pwd, $user['password'])) {
    $_SESSION['error'] = "Incorrect Password";
    $_SESSION['old'] = $_POST;
    header("Location: ../Login.php");
    exit;
}

//Set Session
$_SESSION['user']    = $user['email'];
$_SESSION['user_id'] = $user['id'];
$_SESSION['role']    = $user['role'];
$_SESSION['email']   = $user['email'];

//Role Direct
if($user['role'] === 'admin') {
    header("Location: ../Admin/Admin_Dashboard.php");
    exit;
} elseif ($user['role'] === 'advisor') {
    header("Location: ../Advisor/Advisor_Dashboard.php");
    exit;
} else {
    header("Location: ../Student/Student_Dashboard.php");
    exit;
}
?>