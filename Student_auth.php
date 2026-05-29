<?php
session_start();

//If user are not logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../Login.php");
    exit;
}

//If user is not a student
if($_SESSION['role'] !== 'student') {
    header("Location: ../Login.php");
    exit;
}
?>
