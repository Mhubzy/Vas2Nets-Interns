<?php
session_start();

require_once __DIR__ .'/../classes/User.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Register.php");
    exit;
}

//trim() removes accidental spaces and the "??" prevents undefined index error if a field is missing
$first = trim ($_POST['first'] ?? '');
$last  = trim ($_POST['last']  ?? '');
$email = trim ($_POST['email'] ?? '');
$pwd   = trim ($_POST['pwd']   ?? '');
$pwd2  = trim ($_POST['pwd2']  ?? '');

//This compares both password and correct password
if($pwd !== $pwd2) {  
    $_SESSION['error'] = "Password does not match.";
    $_SESSION['old'] = $_POST;
    header("Location: ../Register.php");
    exit;
} 

//Use User class which handles all other validations internally
$userModel = new User();
$result = $userModel->register($first, $last, $email, $pwd);

//Check if user registration is successful
if($result) {
    $_SESSION['success'] = "Registration Successful. You can now login.";
    header("Location: ../Login.php");
    exit;
} else {
    $_SESSION['error'] = $result['error'];
    $_SESSION['old'] = $_POST;
    header("Location: ../Register.php");
    exit;
}

?>